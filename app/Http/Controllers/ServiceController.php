<?php

namespace App\Http\Controllers;

use App\Models\Profiles\Profile;
use App\Models\Variables\Device;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServiceController extends Controller
{
    public function getDeviceBySerial(Request $request)
    {
        $serial = $request->route('serial');
        $device = Device::with('user')
            ->with('deviceType')
            ->where('serial', $serial)
            ->get()
            ->first();
        if (is_null($device)) throw new NotFoundHttpException('سریال وارد شده اشتباه است.');

        return response()->json([
            'guarantee_end' => $device->guarantee_end,
            'model' => $device->deviceType ? $device->deviceType->name : null,
            'owner' => $device->user ? $device->user->name : null,
        ]);
    }

    public function getProfileByTerminal(Request $request)
    {
        $terminal = $request->route('terminal');
        $profile = Profile::with('customer')
            ->with('business')
            ->with('psp')
            ->with('device')
            ->with('deviceType')
            ->where('terminal_id', $terminal)->get()->first();
        if (is_null($profile)) throw new NotFoundHttpException('ترمینال وارد شده اشتباه است.');

        return response()->json([
            'terminal_id' => $profile->terminal_id,
            'merchant_id' => $profile->merchant_id,
            'device' => [
                'serial' => $profile->device ? $profile->device->serial : null,
                'guarantee_end' => $profile->device ? $profile->device->guarantee_end : null,
                'type' => $profile->deviceType ? $profile->deviceType->name : null,
            ],
            'customer' => [
                'name' => $profile->customer ? $profile->customer->fullName : null,
                'mobile' => $profile->customer ? $profile->customer->mobile : null,
            ],
            'business' => [
                'name' => $profile->business ? $profile->business->name : null,
                'phone' => $profile->business ? $profile->business->fullPhone : null,
                'address' => $profile->business ? sprintf('%s, %s, %s, %s, %s', $profile->business->ostan, $profile->business->shahrestan, $profile->business->bakhsh, $profile->business->shahr, $profile->business->address) : null,
            ],
            'psp' => $profile->psp ? $profile->psp->name : null,
        ]);
    }
}
