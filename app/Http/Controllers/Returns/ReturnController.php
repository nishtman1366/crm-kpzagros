<?php

namespace App\Http\Controllers\Returns;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Payments\PaymentController;
use App\Models\Returns\Event;
use App\Models\Returns\ReturnDevice;
use App\Models\User;
use App\Models\Variables\Accessory;
use App\Models\Variables\DeviceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $returnsQuery = ReturnDevice::with('deviceType')
            ->with('user')
            ->where(function ($query) use ($user) {
                if ($user->isAgent()) {
                    $query->where('user_id', $user->id);
                }
            });;

        $statusId = $request->query('statusId', null);
        if (!is_null($statusId)) {
            $returnsQuery->where(function ($query) use ($statusId) {
                $query->where('status', $statusId);
            });
        }

        $searchQuery = $request->query('query', null);
        if (!is_null($searchQuery)) {
            $returnsQuery->where(function ($query) use ($searchQuery) {
                $query->where('national_code', 'LIKE', '%' . $searchQuery . '%');
                $query->orWhere('name', 'LIKE', '%' . $searchQuery . '%');
                $query->orWhere('mobile', 'LIKE', '%' . $searchQuery . '%');
                $query->orWhere('serial', 'LIKE', '%' . $searchQuery . '%');
                $query->orWhere('tracking_code', 'LIKE', '%' . $searchQuery . '%');
            });
        }

        $userId = $request->query('userId', null);
        if (!is_null($userId)) {
            $returnsQuery->where('user_id', $userId);
        }

        $returns = $returnsQuery->orderBy('updated_at', 'DESC')
            ->paginate(30);

        $paginatedLinks = paginationLinks($returns->appends($request->query->all()));


        $statuses = [
            ['id' => 0, 'name' => 'ثبت موقت'],
            ['id' => 1, 'name' => 'ثبت شده'],
            ['id' => 2, 'name' => 'دریافت شده توسط واحد فنی'],
            ['id' => 3, 'name' => 'در صف امور مالی'],
            ['id' => 4, 'name' => 'عودت هزینه'],
            ['id' => 5, 'name' => 'عودت شده'],
            ['id' => 6, 'name' => 'رد شده'],
        ];

        $returnUsers = ReturnDevice::groupBy('user_id')->pluck('user_id');
        $users = User::whereIn('id', $returnUsers)->where('id', '!=', 1)->get();

        return Inertia::render('Dashboard/Returns/List', [
            'searchQuery' => $searchQuery,
            'statusId' => $statusId,
            'statuses' => $statuses,
            'userId' => $userId,
            'users' => $users,
            'returns' => $returns,
            'paginatedLinks' => $paginatedLinks
        ]);
    }

    public function create(Request $request)
    {
        $accessories = Accessory::where('status', 1)->get();
        $deviceTypes = DeviceType::where('status', 1)->get();
        return Inertia::render('Dashboard/Returns/Create', compact('deviceTypes', 'accessories'));
    }

    public function store(Request $request)
    {
        $request->validateWithBag('newReturnForm', [
            'device_type_id' => 'required',
            'serial' => 'required',
            'name' => 'required',
            'national_code' => 'required|numeric|digits:10',
            'mobile' => 'required|numeric|digits:11|starts_with:09',
            'amount' => 'required|numeric',
            'file' => 'required|file',
        ]);

        $user = Auth::user();
        $request->merge([
            'user_id' => $user->id,
            'tracking_code' => $this->createTrackingCode()
        ]);

        $returnDevice = ReturnDevice::create($request->except(['file']));

        if ($request->hasFile('file')) {
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = date('Y.m.d') . '.' . $extension;
            $request->file('file')->storeAs('returns/faktors/' . $returnDevice->id, $filename, 'public');
            $returnDevice->file = $filename;
            $returnDevice->save();
        }

        $this->saveEvent($user, $returnDevice, 1, null, null);

        return redirect()->route('dashboard.returns.list');
    }

    public function view(Request $request)
    {
        $id = (int)$request->route('returnId');
        $device = ReturnDevice::with('events')
            ->with('events.user')
            ->with('payments')
            ->with('payments.user')
            ->with('payments.type')
            ->find($id);

        if (is_null($device)) throw new NotFoundHttpException('درخواست مورد نظر یافت نشد.');

        $accessories = Accessory::where('status', 1)->get();
        $deviceTypes = DeviceType::where('status', 1)->get();
        return Inertia::render('Dashboard/Returns/View', compact('device', 'deviceTypes', 'accessories'));
    }

    public function update(Request $request)
    {
        $request->validateWithBag('editReturnForm', [
            'device_type_id' => 'required',
            'serial' => 'required',
            'name' => 'required',
            'national_code' => 'required|numeric|digits:10',
            'mobile' => 'required|numeric|digits:11|starts_with:09',
            'amount' => 'required|numeric',
            'file' => 'nullable|file',
        ]);

        $id = (int)$request->route('returnId');
        $device = ReturnDevice::find($id);

        if (is_null($device)) throw new NotFoundHttpException('درخواست مورد نظر یافت نشد.');

        $user = Auth::user();

        $device->fill($request->except(['file']));

        if ($request->hasFile('file')) {
            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = date('Y.m.d') . '.' . $extension;
            $request->file('file')->storeAs('returns/faktors/' . $device->id, $filename, 'public');
            $device->file = $filename;
        }
        $device->save();
        $title = 'اطلاعات پرونده بروزرسانی شد.';
        $status = $device->status;
        $this->saveEvent($user, $device, $status, $title, null);

        return redirect()->back();
    }

    public function updateStatus(Request $request)
    {
        $id = (int)$request->route('returnId');
        $device = ReturnDevice::find($id);

        if (is_null($device)) throw new NotFoundHttpException('درخواست مورد نظر یافت نشد.');

        $user = Auth::user();

        $status = $request->get('status');
        $message = null;
        if ($status == 6) {
            $request->validateWithBag('updateReturnStatusForm', [
                'message' => 'required'
            ]);
        } elseif ($status == 4) {
            $request->validateWithBag('updateReturnStatusForm', [
                'type' => 'required',
                'ref_code' => 'required',
                'payment_date' => 'required',
            ]);

            $payment = PaymentController::createPayment(
                'returns',
                $request->get('type'),
                $device->amount,
                $user->id,
                $device->id,
                null,
                $request->get('ref_code'),
                $request->get('payment_date'),
            );

            $message = sprintf('کد پیگیری پرداخت: %s ، تاریخ پرداخت: %s', $request->get('ref_code'), $payment->jDate);
        }

        $device->status = $status;
        $device->save();
        $this->saveEvent($user, $device, $status, null, $request->get('message', $message));

        return redirect()->back();
    }

    private function createTrackingCode()
    {
        $trackingCode = mt_rand(111111, 999999);

        $trackingCodeExistence = ReturnDevice::where('tracking_code', $trackingCode)->exists();
        if ($trackingCodeExistence) return $this->createTrackingCode();

        return $trackingCode;
    }

    private function saveEvent($user, $device, $status, $title = null, $message = null, $notification = true)
    {
        if (is_null($title)) {
            switch ($status) {
                default:
                case 1:
                    $title = sprintf('درخواست عودت دستگاه ثبت شد.');
                    break;
                case 2:
                    $title = sprintf('دستگاه توسط واحد فنی دریافت شد.');
                    break;
                case 3:
                    $title = sprintf('دستگاه در صف امور مالی قرار گرفت.');
                    break;
                case 4:
                    $title = sprintf('هزینه دستگاه پرداخت شد.');
                    break;
                case 5:
                    $title = sprintf('دستگاه عودت داده شد.');
                    break;
                case 6:
                    $title = sprintf('عودت دستگاه به علت دلایل مطرح شده مورد تایید نمی باشد.');
                    break;
            }
        }
        Event::create([
            'user_id' => $user->id,
            'return_device_id' => $device->id,
            'status' => $status,
            'title' => $title,
            'description' => $message
        ]);
//        if ($notification) {
//            NotificationController::handleProfileNotifications('REPAIRS', $repair, $user);
//        }
    }
}
