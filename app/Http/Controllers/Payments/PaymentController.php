<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Payments\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public static function createPayment($type, $typeId, $amount, $userId, $modelId, $req_code, $ref_code, $date = null, $status = 1)
    {
        if($type=='repairs') {
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
        }elseif($type=='returns'){
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

        if (is_null($payment)) return response()->json('اظلاعات پرداخت یافت نشد.');
        $payment->status = 2;
        $payment->save();

        if (!is_null($payment->profile_id)) return redirect()->route('dashboard.profiles.view', ['profileId' => $payment->profile_id]);
        elseif (!is_null($payment->repair_id)) return redirect()->route('dashboard.repairs.view', ['repairId' => $payment->repair_id]);
        elseif (!is_null($payment->return_device_id)) return redirect()->route('dashboard.returns.view', ['returnId' => $payment->return_device_id]);
    }
}
