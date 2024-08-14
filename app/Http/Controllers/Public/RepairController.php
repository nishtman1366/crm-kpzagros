<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\Payments\PaymentController;
use App\Http\Requests\Repairs\CreateRepair;
use App\Models\Repairs\Event;
use App\Models\Repairs\Repair;
use App\Models\Repairs\RepairTypesList;
use App\Models\Repairs\Type;
use App\Models\User;
use App\Models\Variables\Accessory;
use App\Models\Variables\Bank;
use App\Models\Variables\DeviceType;
use App\Models\Variables\Psp;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class RepairController extends Controller
{
    public function index()
    {
        return Inertia::render('Public/Repair/Index');
    }

    public function create()
    {
        $deviceTypes = DeviceType::where('status', 1)->orderBy('name', 'ASC')->get();
        $psps = Psp::where('status', 1)->orderBy('name', 'ASC')->get();
        $repairTypes = Type::where('status', 1)->orderBy('name', 'ASC')->get();
        $banks = Bank::where('status', 1)->orderBy('name', 'ASC')->get();
        $accessories = Accessory::where('status', 1)->get();


        return Inertia::render('Public/Repair/Create', compact('deviceTypes', 'psps', 'banks', 'repairTypes', 'accessories'));
    }

    public function store(CreateRepair $request)
    {
        $user = User::find(2);

        $request->merge([
            'user_id' => $user->id,
            'tracking_code' => $this->createTrackingCode()
        ]);

        $repair = Repair::create($request->all());
        $typeList = $request->get('repairTypeList', []);
        foreach ($typeList as $item) {
            RepairTypesList::create(['repair_id' => $repair->id, 'type_id' => $item]);
        }
        $this->saveEvent($user, $repair, 1, null, null);

        return redirect()->route('public.repairs.view', ['trackingCode' => $repair->tracking_code]);
    }

    private function createTrackingCode()
    {
        $trackingCode = mt_rand(111111, 999999);

        $trackingCodeExistence = Repair::where('tracking_code', $trackingCode)->exists();
        if ($trackingCodeExistence) return $this->createTrackingCode();

        return $trackingCode;
    }

    public static function saveEvent($user, $repair, $status, $title = null, $message = null, $notification = true)
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
                case 8:
                    $title = sprintf('دستگاه قابل تعمیر نمی باشد.');
                    break;
            }
        }
        Event::create([
            'user_id' => $user->id,
            'repair_id' => $repair->id,
            'status' => $status,
            'title' => $title,
            'description' => $message
        ]);
        if ($notification) {
            NotificationController::handleProfileNotifications('REPAIRS', $repair, $user);
        }
    }

    public function update(Repair $repair, Request $request)
    {
        $request->validateWithBag('submitPaymentForm', [
            'ref_code' => 'required',
            'payment_date' => 'required|date',
        ]);
        $user = User::find(2);
        $typeId = $request->get('type');
        $ref_code = $request->get('ref_code');
        $date = $request->get('payment_date');
        $payment = PaymentController::createPayment(
            'repairs',
            $typeId,
            $repair->price,
            2,
            $repair->id,
            null,
            $ref_code,
            $date,
        );

        $message = sprintf('کد پیگیری پرداخت: %s ، تاریخ پرداخت: %s', $ref_code, $payment->jDate);
        $repair->save();

        $this->saveEvent($user, $repair, 6, 'ثبت پرداخت در وب‌سایت', $message, true);

        return redirect()->back();
    }

    public function checkRepair(Request $request)
    {
        $request->validateWithBag('repairTrackingForm', [
            'national_code' => 'required',
            'tracking_code' => 'required'
        ], [
            'tracking_code.required' => 'کد رگیری الزامیست'
        ]);
        $nationalCode = toEnglishNumbers($request->get('national_code'));
        $trackingCode = toEnglishNumbers($request->get('tracking_code'));

        $repair = Repair::where('tracking_code', $trackingCode)
            ->where('national_code', $nationalCode)
            ->get()
            ->first();
        if (is_null($repair)) throw ValidationException::withMessages(['report' => 'درخواست موردنظر یافت نشد.'])->errorBag('repairTrackingForm');

        $request->session()->flash('checkRepair', true);
        return redirect()->route('public.repairs.view', ['trackingCode' => $trackingCode, 'nationalCode' => $nationalCode]);
    }

    public function view($trackingCode, $nationalCode, Request $request)
    {
//        if (!($request->session()->has('checkRepair') && $request->session()->get('checkRepair') === true)) {
//            throw ValidationException::withMessages(['report' => 'درخواست موردنظر یافت نشد.'])->errorBag('repairTrackingForm');
//        }
        $repair = Repair::with('events')
            ->with('deviceType')
            ->with('events.user')
            ->with('payments')
            ->with('payments.user')
            ->with('payments.type')
            ->where('tracking_code', $trackingCode)
            ->where('national_code', $nationalCode)
            ->get()
            ->first();


        if (is_null($repair)) throw ValidationException::withMessages(['report' => 'درخواست موردنظر یافت نشد.'])->errorBag('repairTrackingForm');

        $repairTypesList = RepairTypesList::with('type')->where('repair_id', $repair->id)->get();

        return Inertia::render('Public/Repair/View', compact('repair', 'repairTypesList'));
    }
}
