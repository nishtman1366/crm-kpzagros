<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Variables\Device;
use App\Models\Variables\DeviceConnectionType;
use App\Models\Variables\DevicePsp;
use App\Models\Variables\DeviceType;
use App\Models\Variables\Psp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $searchQuery = $request->query('query', null);
        $deviceTypes = DeviceType::with('type')->with('psps')->where(function ($query) use ($searchQuery) {
            if (!is_null($searchQuery)) {
                $query->where('name', 'LIKE', '%' . $searchQuery . '%');
            }
        })->orderBy('name')->get();
        $deviceTypes->each(function ($device) use ($user) {
            $device->physicalStatus1Count = Device::where('device_type_id', $device->id)
                ->where('physical_status', 1)
                ->where(function ($query) use ($user) {
                    if ($user->isAdmin()) {
                        $query->where('user_id', $user->id);
                        $query->orWhereHas('user', function ($userQuery) use ($user) {
                            $userQuery->where('parent_id', $user->id);
                        });
                    }
                })->count();
            $device->physicalStatus2Count = Device::where('device_type_id', $device->id)
                ->where('physical_status', 2)
                ->where(function ($query) use ($user) {
                    if ($user->isAdmin()) {
                        $query->where('user_id', $user->id);
                        $query->orWhereHas('user', function ($userQuery) use ($user) {
                            $userQuery->where('parent_id', $user->id);
                        });
                    }
                })->count();
        });

        $connectionTypes = DeviceConnectionType::orderBy('id', 'ASC')->get();

        $psps = Psp::orderBy('name', 'ASC')->get();
        return Inertia::render('Dashboard/Settings/Devices', [
            'searchQuery' => $searchQuery,
            'deviceTypes' => $deviceTypes,
            'deviceConnectionTypes' => $connectionTypes,
            'psps' => $psps
        ]);
    }

    public function store(Request $request)
    {
        $request->validateWithBag('deviceForm', [
            'name' => 'required',
            'device_connection_type_id' => 'required',
            'status' => 'required'
        ]);

        $deviceType = Devicetype::create($request->all());
        $psps = $request->get('psps');
        foreach ($psps as $psp) {
            DevicePsp::create([
                'device_type_id' => $deviceType->id,
                'psp_id' => $psp
            ]);
        }

        return redirect()->route('dashboard.settings.devices.list');
    }

    public function update(Request $request)
    {
        $id = $request->route('deviceId');
        $request->validateWithBag('deviceForm', [
            'name' => 'required',
            'device_connection_type_id' => 'required',
            'status' => 'required'
        ]);
        $deviceType = DeviceType::find($id);
        if (is_null($deviceType)) throw new NotFoundHttpException('دستگاه یافت نشد.');
        $deviceType->fill($request->all());
        $deviceType->save();

        $psps = $request->get('psps');
        DevicePsp::where('device_type_id', $deviceType->id)->delete();
        foreach ($psps as $psp) {
            DevicePsp::create([
                'device_type_id' => $deviceType->id,
                'psp_id' => $psp
            ]);
        }

        return redirect()->route('dashboard.settings.devices.list');
    }

    public function destroy(Request $request)
    {
        $id = $request->route('deviceId');
        $device = DeviceType::find($id);
        if (is_null($device)) throw new NotFoundHttpException('دستگاه یافت نشد.');
        $device->delete();
        return redirect()->route('dashboard.settings.devices.list');
    }
}
