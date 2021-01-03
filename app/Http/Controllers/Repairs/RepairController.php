<?php

namespace App\Http\Controllers\Repairs;

use App\Exports\Profiles\ProfileExport;
use App\Exports\Repairs\RepairExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Payments\PaymentController;
use App\Http\Requests\Repairs\CreateRepair;
use App\Models\Repairs\Event;
use App\Models\Repairs\Repair;
use App\Models\Repairs\Location;
use App\Models\Repairs\RepairTypesList;
use App\Models\Repairs\Type;
use App\Models\Variables\DeviceType;
use App\Models\Variables\Psp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;

class RepairController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $repairsQuery = Repair::with('user')
            ->with('psp')
            ->with('deviceType')
            ->where(function ($query) use ($user) {
                if ($user->isAgent()) {
                    $query->where('user_id', $user->id);
                }
            });

        $statusId = $request->query('statusId', null);
        if (!is_null($statusId)) {
            $repairsQuery->where(function ($query) use ($statusId) {
                $query->where('status', $statusId);
            });
        }

        $searchQuery = $request->query('query', null);
        if (!is_null($searchQuery)) {
            $repairsQuery->where(function ($query) use ($searchQuery) {
                $query->where('national_code', 'LIKE', '%' . $searchQuery . '%');
                $query->orWhere('name', 'LIKE', '%' . $searchQuery . '%');
                $query->orWhere('mobile', 'LIKE', '%' . $searchQuery . '%');
                $query->orWhere('serial', 'LIKE', '%' . $searchQuery . '%');
                $query->orWhere('tracking_code', 'LIKE', '%' . $searchQuery . '%');
            });
        }


        $fromDate = $request->query('fromDate', Jalalian::now()->subDays(7)->format('Y-m-d'));
        $fromDate = str_replace('/', '-', $fromDate);
        $jFromDate = $fromDate;
        $fromDate = Jalalian::fromFormat('Y-m-d', $fromDate)->toCarbon()->hour(0)->minute(0)->second(0);
        $repairsQuery->where('created_at', '>=', $fromDate);

        $toDate = $request->query('toDate', Jalalian::now()->format('Y-m-d'));
        $toDate = str_replace('/', '-', $toDate);
        $jToDate = $toDate;
        $toDate = Jalalian::fromFormat('Y-m-d', $toDate)->toCarbon()->hour(23)->minute(59)->second(59);
        $repairsQuery->where('created_at', '<=', $toDate);

        $repairs = $repairsQuery->orderBy('id', 'DESC')
            ->paginate(30);
        $paginatedLinks = paginationLinks($repairs->appends($request->query->all()));

        $statuses = [
            ['id' => 0, 'name' => 'ثبت موقت'],
            ['id' => 1, 'name' => 'ثبت شده'],
            ['id' => 2, 'name' => 'دریافت شده توسط واحد فنی'],
            ['id' => 3, 'name' => 'در صف تعمیر'],
            ['id' => 4, 'name' => 'تعمیر شده'],
            ['id' => 5, 'name' => 'در انتظار پرداخت'],
            ['id' => 6, 'name' => 'پرداخت شده'],
            ['id' => 7, 'name' => 'عودت شده']
        ];
        return Inertia::render('Dashboard/Repairs/List', [
            'repairs' => $repairs,
            'searchQuery' => $searchQuery,
            'statusId' => $statusId,
            'statuses' => $statuses,
            'fromDate' => $jFromDate,
            'toDate' => $jToDate,
            'paginatedLinks' => $paginatedLinks,

        ]);
    }

    public function create(Request $request)
    {
        $deviceTypes = DeviceType::where('status', 1)->orderBy('name', 'ASC')->get();
        $psps = Psp::where('status', 1)->orderBy('name', 'ASC')->get();
        $repairTypes = Type::where('status', 1)->orderBy('name', 'ASC')->get();
        return Inertia::render('Dashboard/Repairs/Create', [
            'deviceTypes' => $deviceTypes,
            'psps' => $psps,
            'repairTypes' => $repairTypes,
        ]);
    }

    public function store(CreateRepair $request)
    {
        $request->merge(['tracking_code' => $this->createTrackingCode()]);
        $repair = Repair::create($request->all());
        $typeList = $request->get('repairTypeList', []);
        foreach ($typeList as $item) {
            RepairTypesList::create(['repair_id' => $repair->id, 'type_id' => $item]);
        }

        return redirect()->route('dashboard.repairs.list');
    }

    public function view(Request $request)
    {
        $id = $request->route('repairId');
        $repair = Repair::with('events')
            ->with('events.user')
            ->with('payments')
            ->find($id);

        if (is_null($repair)) return response()->json(['message' => 'اطلاعات درخواست یافت نشد'], 404);
        $repairTypesList = RepairTypesList::where('repair_id', $id)->pluck('type_id');
        $locations = Location::orderBy('name', 'ASC')->get();
        $deviceTypes = DeviceType::where('status', 1)->orderBy('name', 'ASC')->get();
        $psps = Psp::where('status', 1)->orderBy('name', 'ASC')->get();
        $repairTypes = Type::where('status', 1)->orderBy('name', 'ASC')->get();
        return Inertia::render('Dashboard/Repairs/View', [
            'repair' => $repair,
            'repairTypesList' => $repairTypesList,
            'locations' => $locations,
            'deviceTypes' => $deviceTypes,
            'psps' => $psps,
            'repairTypes' => $repairTypes,
        ]);
    }

    public function update(Request $request)
    {
        $id = $request->route('repairId');
        $repair = Repair::find($id);
        if (is_null($repair)) return response()->json(['message' => 'اطلاعات درخواست یافت نشد'], 404);

        $user = Auth::user();
        $title = null;
        $message = null;
        if ($request->has('status') && $request->get('status') > 1) {
            $status = $request->get('status');
            $repair->status = $status;

            if ($status == 6) {
                $request->validateWithBag('submitPaymentForm', [
                    'ref_code' => 'required',
                    'payment_date' => 'required|date',
                ]);

                $typeId = $request->get('type');
                $ref_code = $request->get('ref_code');
                $date = $request->get('payment_date');
                $payment = PaymentController::createPayment(
                    $typeId,
                    $repair->price,
                    $user->id,
                    null,
                    $repair->id,
                    null,
                    $ref_code,
                    $date,
                );

                $message = sprintf('کد پیگیری پرداخت: %s ، تاریخ پرداخت: %s', $ref_code, $payment->jDate);
            }
        } else {
            $title = 'اطلاعات پرونده بروزرسانی شد.';
            $status = $repair->status;
            $repair->fill($request->all());
        }
        $repair->save();

        $this->saveEvent($user, $repair->id, $status, $title, $request->get('message', $message));

        return redirect()->route('dashboard.repairs.list');
    }

    private function createTrackingCode()
    {
        $trackingCode = mt_rand(111111, 999999);

        $trackingCodeExistence = Repair::where('tracking_code', $trackingCode)->exists();
        if ($trackingCodeExistence) return $this->createTrackingCode();

        return $trackingCode;
    }

    private function saveEvent($user, $repairId, $status, $title = null, $message = null)
    {
        if (is_null($title)) {
            switch ($status) {
                default:
                case 1:
                    $title = sprintf('درخواست تعمیرات ثبت شد.');
                    break;
                case 2:
                    $title = sprintf('دستگاه توسط واحد فنی دریافت شد.');
                    break;
                case 3:
                    $title = sprintf('دستگاه در صف تعمیر قرار گرفت.');
                    break;
                case 4:
                    $title = sprintf('فرایند تعمیر دستگاه به پایان رسید.');
                    break;
                case 5:
                    $title = sprintf('دستگاه در انتظار پرداخت هزینه تعمیرات می باشد.');
                    break;
                case 6:
                    $title = sprintf('هزینه تعمیرات پرداخت شد.');
                    break;
                case 7:
                    $title = sprintf('دستگاه عودت داده شد.');
                    break;
            }
        }
        Event::create([
            'user_id' => $user->id,
            'repair_id' => $repairId,
            'status' => $status,
            'title' => $title,
            'description' => $message
        ]);
    }

    public function downloadExcel(Request $request)
    {
        $user = Auth::user();
        $repairsQuery = Repair::with('user')
            ->with('psp')
            ->with('deviceType')
            ->where(function ($query) use ($user) {
                if ($user->isAgent()) {
                    $query->where('user_id', $user->id);
                }
            });

        $statusId = $request->query('statusId', null);
        if (!is_null($statusId)) {
            $repairsQuery->where(function ($query) use ($statusId) {
                $query->where('status', $statusId);
            });
        }

        $searchQuery = $request->query('query', null);
        if (!is_null($searchQuery)) {
            $repairsQuery->where(function ($query) use ($searchQuery) {
                $query->where('national_code', 'LIKE', '%' . $searchQuery . '%');
                $query->orWhere('name', 'LIKE', '%' . $searchQuery . '%');
                $query->orWhere('mobile', 'LIKE', '%' . $searchQuery . '%');
                $query->orWhere('serial', 'LIKE', '%' . $searchQuery . '%');
                $query->orWhere('tracking_code', 'LIKE', '%' . $searchQuery . '%');
            });
        }


        $fromDate = $request->query('fromDate', Jalalian::now()->subDays(7)->format('Y-m-d'));
        $fromDate = str_replace('/', '-', $fromDate);
        $fromDate = Jalalian::fromFormat('Y-m-d', $fromDate)->toCarbon()->hour(0)->minute(0)->second(0);
        $repairsQuery->where('created_at', '>=', $fromDate);

        $toDate = $request->query('toDate', Jalalian::now()->format('Y-m-d'));
        $toDate = str_replace('/', '-', $toDate);
        $toDate = Jalalian::fromFormat('Y-m-d', $toDate)->toCarbon()->hour(23)->minute(59)->second(59);
        $repairsQuery->where('created_at', '<=', $toDate);

        $repairs = $repairsQuery->orderBy('id', 'DESC')->get();

        $jDate = Jalalian::forge(now())->format('Y.m.d');
        return Excel::download(new RepairExport($repairs), 'repairs.' . $jDate . '.xlsx');
    }
}
