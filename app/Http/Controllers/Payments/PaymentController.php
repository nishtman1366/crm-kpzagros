<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Repairs\RepairController;
use App\Libraries\Payments\ZarinPal\ZarinPal;
use App\Models\Payments\Payment;
use App\Models\Repairs\Repair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class PaymentController extends Controller
{

    public static function createPayment($type, $typeId, $amount, $userId, $modelId, $req_code, $ref_code, $date = null, $status = 1)
    {
        if ($type == 'repairs') {
            $payment = Payment::create([
                'type_id' => $typeId,
                'user_id' => $userId,
                'repair_id' => $modelId,
                'req_code' => $req_code,
                'ref_code' => $ref_code,
                'date' => $date,
                'tracking_code' => static::createTrackingCode(),
                'status' => $status,
                'amount' => $amount,
            ]);
        } elseif ($type == 'returns') {
            $payment = Payment::create([
                'type_id' => $typeId,
                'user_id' => $userId,
                'return_device_id' => $modelId,
                'req_code' => $req_code,
                'ref_code' => $ref_code,
                'date' => $date,
                'tracking_code' => static::createTrackingCode(),
                'status' => $status,
                'amount' => $amount,
            ]);
        }

        return $payment;
    }

    private static function createTrackingCode()
    {
        $trackingCode = mt_rand(111111, 999999);

        $trackingCodeExistence = Payment::where('tracking_code', $trackingCode)->exists();
        if ($trackingCodeExistence) return static::createTrackingCode();

        return $trackingCode;
    }

    public function confirm(Request $request)
    {
        $id = $request->route('paymentId');
        $payment = Payment::find($id);

        if (is_null($payment)) throw new NotFoundHttpException('اظلاعات پرداخت یافت نشد.');

        $route = $this->confirmPayment($payment);
        return redirect()->to($route);
    }

    public function ipgRequest(Request $request)
    {
        $type = $request->route('type');
        $id = $request->route('id');
        $amount = 0;
        $description = '';
        if ($type == 'repairs') {
            $repair = Repair::find((int)$id);
            if (is_null($repair)) throw new NotFoundHttpException('پرونده تعمیراتت یافت نشد.');
            $amount = $repair->price;
            $description = sprintf('پرونده تعمیرات %s (%s)', $repair->tracking_code, $repair->name);
        }

        $payment = $this->createPayment($type, 1, $amount, Auth::id(), $id, null, null, now()->format('Y-m-d H:i:s'), 0);
        $zarinpal = new ZarinPal(config('services.zarinpal.merchantID', config('Zarinpal.merchantID', 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX')));
        if (config('services.zarinpal.sandbox', false)) {
            $zarinpal->enableSandbox();
        }
        if (config('services.zarinpal.zarinGate', false)) {
            $zarinpal->isZarinGate();
        }
        $zarinpal->request(route('dashboard.payments.ipg.verify', ['trackingCode' => $payment->tracking_code]), $amount, $description);
        if ($zarinpal->Authority) {
            $payment->req_code = $zarinpal->Authority;
            $payment->save();
            session()->flash('paymentRequest', ['status' => 'success', 'redirectUrl' => sprintf($zarinpal->redirectUrl, $zarinpal->Authority)]);
            return redirect()->back();
        } else {
            throw new UnauthorizedHttpException('', 'خطا در برقراری ارتباط با درگاه پرداخت');
        }
    }

    public function ipgVerify(Request $request)
    {
        $payment = Payment::where('tracking_code', $request->route('trackingCode'))->get()->first();
        if (is_null($payment)) throw new NotFoundHttpException('اطلاعات پرداخت یافت نشد.');

        if ($payment->status !== 2) {
            $zarinpal = new ZarinPal(config('services.zarinpal.merchantID', config('Zarinpal.merchantID', 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX')));
            if (config('services.zarinpal.sandbox', false)) {
                $zarinpal->enableSandbox();
            }
            if (config('services.zarinpal.zarinGate', false)) {
                $zarinpal->isZarinGate();
            }
            $result = $zarinpal->verify($payment->amount, $payment->req_code);
            if ($result && ($result['Status'] === 'success' || $result['Status'] === 'verified_before')) {
                $route = $this->confirmPayment($payment, 'ok', $result['RefID']);
            } elseif ($result && $result['Status'] === 'error') {
                $route = $this->confirmPayment($payment, 'error', $result['error']);
            } else {
                $route = 'dashboard';
            }
        }

        return redirect()->to($route);
    }

    private function confirmPayment(Payment $payment, $status = 'ok', $refCode = null)
    {
        $payment->status = $status === 'ok' ? 2 : 255;
        if (!is_null($refCode)) {
            if ($status === 'ok') {
                $payment->ref_code = $refCode;
            } else {
                $payment->description = $refCode;
            }
        }
        $payment->save();

        $route = 'dashboard';
        if (!is_null($payment->profile_id)) {
            $route = route('dashboard.profiles.view', ['profileId' => $payment->profile_id]);
        } elseif (!is_null($payment->repair_id)) {
            if ($status === 'ok') {
                $payment->repair->status = 6;
                $payment->repair->save();
                RepairController::saveEvent($payment->user, $payment->repair, 6, 'پرداخت هزینه تعمیرات', sprintf('کد پیگیری پرداخت: %s ، تاریخ پرداخت: %s', $refCode, $payment->jDate), true);
            }
            $route = route('dashboard.repairs.view', ['repairId' => $payment->repair_id]);
        } elseif (!is_null($payment->return_device_id)) {
            $route = route('dashboard.returns.view', ['returnId' => $payment->return_device_id]);
        }
        return $route;
    }
}
