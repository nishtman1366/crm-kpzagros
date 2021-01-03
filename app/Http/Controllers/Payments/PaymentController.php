<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Payments\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public static function createPayment($typeId, $amount, $userId, $profileId, $repairId, $req_code, $ref_code, $date = null, $status = 1)
    {
        $payment = Payment::create([
            'type_id' => $typeId,
            'user_id' => $userId,
            'profile_id' => $profileId,
            'repair_id' => $repairId,
            'req_code' => $req_code,
            'ref_code' => $ref_code,
            'date' => $date,
            'tracking_code' => static::createTrackingCode(),
            'status' => $status,
            'amount' => $amount,
        ]);

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
    }
}
