<?php

namespace App\Http\Controllers\Profiles;

use App\Http\Controllers\Controller;
use App\Models\Profiles\Profile;
use App\Models\Profiles\Terminal;
use App\Models\Variables\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TerminalController extends Controller
{
    public function updateSerial(Profile $profile, Terminal $terminal, Request $request)
    {
        $request->validateWithBag('serialForm', [
            'device_id' => 'required|exists:devices,id'
        ]);
        if ($terminal->profile_id !== $profile->id) throw new UnauthorizedHttpException('', 'دسترسی غیرمجاز');

        if ($terminal->status === 7) {
            $terminal->new_device_id = (int)$request->get('device_id');
            $terminal->status = 8;
            $terminal->save();
        } elseif ($terminal->status === 8) {

        } else {
            $request->merge(['reject_reason' => null]);
            $terminal->fill($request->all());
            $terminal->save();

            $deviceId = (int)$request->get('device_id');
            $device = Device::find($deviceId);
            $device->transport_status = 2;
            $device->save();

            $profile->status = 6;
            $profile->save();
            $user = Auth::user();
            setProfileMessage(6, $user, $profile, null);
        }


        return redirect()->back();
    }

    public function terminalNumber(Profile $profile, Terminal $terminal, Request $request)
    {
        $request->validateWithBag('terminalForm', [
            'terminal_number' => 'required|unique:terminals,terminal_number'
        ]);
        if ($terminal->profile_id !== $profile->id) throw new UnauthorizedHttpException('', 'دسترسی غیرمجاز');

        $terminal->fill($request->all());
        $terminal->save();

        $profile->status = 7;
        $profile->save();
        $user = Auth::user();
        setProfileMessage(7, $user, $profile, null);

        return redirect()->back();
    }

    public function rejectSerial(Profile $profile, Terminal $terminal, Request $request)
    {
        $request->validateWithBag('rejectSerialForm', [
            'reject_reason' => 'required',
        ]);
        $terminal->fill($request->all());
        $terminal->save();

        $device = Device::find($terminal->device_id);
        $device->transport_status = 1;
        $device->psp_status = 1;
        $device->save();

        $user = Auth::user();
        setProfileMessage(13, $user, $profile, null);

        return redirect()->back();
    }

    public function install(Profile $profile, Terminal $terminal, Request $request)
    {
        $request->merge(['setup_date' => now()]);
        $terminal->fill($request->all());
        $terminal->save();
        $user = Auth::user();

        $terminal->device->transport_status = 3;
        $terminal->device->psp_status = 2;
        $terminal->device->save();

        setProfileMessage(8, $user, $profile, null);
        return redirect()->back();
    }

    public function cancelSerial(Profile $profile, Terminal $terminal, Request $request)
    {
        $request->validateWithBag('cancelRequestForm', [
            'cancel_reason' => 'required',
        ]);

        $user = Auth::user();
        $terminal->fill($request->all());
        $terminal->save();

        setProfileMessage(12, $user, $profile, null);
        return redirect()->back();
    }

    public function confirmCancelSerial(Profile $profile, Terminal $terminal, Request $request)
    {
        $status = (int)$request->get('status');
        $validationArray = [
            'status' => 'required'
        ];
        if ($status === 10) {
            $validationArray = array_merge(['cancelCancelMessage' => 'required']);
            $status = 6;
        }
        $request->validateWithBag('confirmCancelForm', $validationArray, [
            'cancelCancelMessage.required' => 'علت رد درخواست ابطال ضروری است'
        ]);

        $terminal->status = $status;
        $terminal->save();

        if ($status === 5) {
            $terminal->device->transport_status = 1;
            $terminal->device->psp_status = 1;
            $terminal->device->save();
        }

        $user = Auth::user();
        setProfileMessage($status == 5 ? 9 : 20, $user, $profile, $request->get('cancelCancelMessage'));
        return redirect()->back();
    }

    public function changeSerial(Profile $profile, Terminal $terminal, Request $request)
    {
        $request->validateWithBag('changeSerialRequestForm', [
            'change_reason' => 'required',
            'new_device_type_id' => 'required',
        ]);

        $user = Auth::user();
        $terminal->fill($request->all());
        $terminal->save();

        setProfileMessage(14, $user, $profile, null);
        return redirect()->back();
    }

    public function confirmChangeSerial(Profile $profile, Terminal $terminal, Request $request)
    {
        $status = (int)$request->get('status');
        $eventStatus = 17;
        $validationArray = [
            'status' => 'required'
        ];
        if ($status === 9) {
            $validationArray = array_merge(['cancelChangeMessage' => 'required']);
            $status = 6;
            $eventStatus = 16;
        }
        $request->validateWithBag('confirmChangeSerialForm', $validationArray, [
            'cancelChangeMessage.required' => 'علت رد درخواست جابجایی ضروری است'
        ]);

        if ($status === 3) {
            $oldDevice = Device::find($terminal->device_id);
            if (!is_null($oldDevice)) {
                $oldDevice->transport_status = 1;
                $oldDevice->psp_status = 1;
                $oldDevice->save();
            }

            $newDevice = Device::find($terminal->new_device_id);
            if (!is_null($newDevice)) {
                $newDevice->transport_status = 2;
                $newDevice->psp_status = 2;
                $newDevice->save();
            }
        }

        $terminal->device_type_id = $terminal->new_device_type_id;
        $terminal->device_id = $terminal->new_device_id;
        $terminal->new_device_type_id = null;
        $terminal->new_device_id = null;
        $terminal->change_reason = null;
        $terminal->status = $status;
        $terminal->save();

        $user = Auth::user();
        setProfileMessage($eventStatus, $user, $profile, $request->get('cancelChangeMessage'));
        return redirect()->back();
    }
}
