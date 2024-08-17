<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
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
use App\Notifications\ProfileNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
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
        $transportTypes = [
            ['key' => 'POST', 'title' => 'ارسال به‌وسیله باربری'],
            ['key' => 'OFFICE', 'title' => 'تحویل حضوری']
        ];

        return Inertia::render('Public/Repair/Create', compact('deviceTypes', 'psps', 'banks', 'repairTypes', 'accessories', 'transportTypes'));
    }

    public function store(CreateRepair $request)
    {
        $user = User::find(2);
        $request->validateWithBag('newRepairForm', [
            'transport_type' => 'required|in:POST,OFFICE',
            'transport_description' => 'required'
        ], [
            'transport_description.required' => sprintf('%s را وارد نمایید', $request->get('transport_type') === 'POST' ? 'کد رهگیری پست یا باربری' : 'نام دفتر یا شخص تحویل‌گیرنده')
        ]);
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
        $user = new User();
        $user->mobile = $repair->mobile;
        $type = new \App\Models\Notifications\Type();
        $type->pattern = 680770;
        $type->body = 'پذیرنده ارجمند، درخواست تعمیر برای دستگاه کارتخوان شما با شماره سریال #SERIAL# و کد رهگیری #TRACKING_CODE# در وب سایت شرکت زاگرس پی به نشانی zagrospay.com ثبت شده است. بدیهی است تا زمانیکه پیامک دریافت دستگاه توسط شرکت را دریافت نکرده باشید، مسئولیت پیگیری ارسال و تحویل مرسوله به شرکت بر عهده پذیرنده است. در صورت نیاز به راهنمایی بیشتر می توانید با شماره 08331420000 واحد خدمات پس از فروش زاگرس پی تماس بگیرید.';
//        $user->notify(new ProfileNotification($type, ['SERIAL' => $repair->serial, 'TRACKING_CODE' => $repair->tracking_code], false));
        Notification::send([$user], new ProfileNotification($type, ['SERIAL' => $repair->serial, 'TRACKING_CODE' => $repair->tracking_code], false));

        $request->session()->flash('repair_message', [
            'ثبت اطلاعات با موفقیت انجام‌شد.',
            'کد رهگیری درخواست شما',
            $repair->tracking_code,
            'شما می‌توانید جهت پیگیری درخواست خود در هر زمان از طریق همین صفحه اقدام نمایید.'
        ]);
        return [];
        return redirect()->route('public.repairs.index');
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
//        if ($notification) {
//            NotificationController::handleProfileNotifications('REPAIRS', $repair, $user);
//        }
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
            'tracking_code.required' => 'کد رهگیری الزامیست'
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
