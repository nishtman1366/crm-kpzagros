<?php

namespace App\Http\Controllers;

use App\Exports\Devices\DeviceExport;
use App\Imports\Devices\DeviceImport;
use App\Models\Profiles\Profile;
use App\Models\User;
use App\Models\Variables\Device;
use App\Models\Variables\DeviceConnectionType;
use App\Models\Variables\DeviceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $devicesQuery = Device::with('deviceType')
            ->with('deviceType.type')
            ->with('user')
            ->where(function ($query) use ($user) {
                if ($user->isAgent() || $user->isAdmin()) {
                    $query->where(function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
                }
                if ($user->isAdmin()) {
                    $query->orWhereHas('user', function ($query) use ($user) {
                        $query->where('parent_id', $user->id);
                    });
                }
            });

        $searchQuery = $request->query('searchQuery', null);
        if (!is_null($searchQuery)) {
            $devicesQuery->where(function ($query) use ($searchQuery) {
                $query->where('serial', 'LIKE', '%' . $searchQuery . '%');
            });
        }

        $modelId = $request->query('modelId', '');
        if ($modelId != '') {
            $devicesQuery->where(function ($query) use ($modelId) {
                $query->where('device_type_id', $modelId);
            });
        }
        $typeId = $request->query('typeId', '');
        if ($typeId != '') {
            $deviceTypes = DeviceType::where('device_connection_type_id', $typeId)->pluck('id');
            $devicesQuery->whereIn('device_type_id', $deviceTypes);
        }

        $physicalStatus = $request->query('physicalStatus', '');
        if ($physicalStatus != '') {
            $devicesQuery->where(function ($query) use ($physicalStatus) {
                $query->where('physical_status', $physicalStatus);
            });
        }

        $transportStatus = $request->query('transportStatus', '');
        if ($transportStatus != '') {
            $devicesQuery->where(function ($query) use ($transportStatus) {
                $query->where('transport_status', $transportStatus);
            });
        }

        $pspStatus = $request->query('pspStatus', '');
        if ($pspStatus != '') {
            $devicesQuery->where(function ($query) use ($pspStatus) {
                $query->where('psp_status', $pspStatus);
            });
        }

        $deviceStatus = $request->query('deviceStatus', '');
        if ($deviceStatus != '') {
            $devicesQuery->where(function ($query) use ($deviceStatus) {
                $query->where('status', $deviceStatus);
            });
        }

        $devices = $devicesQuery->orderBy('id', 'ASC')->paginate()->appends(['typeId' => $typeId, 'modelId' => $modelId, 'searchQuery' => $searchQuery, 'physicalStatus' => $physicalStatus, 'transportStatus' => $transportStatus, 'pspStatus' => $pspStatus,]);
        $paginatedLinks = paginationLinks($devices);


        $models = DeviceType::orderBy('name', 'ASC')->get();

        $types = DeviceConnectionType::orderBy('id', 'ASC')->get();

        return Inertia::render('Dashboard/Devices/DevicesList', [
            'devices' => $devices,
            'models' => $models,
            'types' => $types,
            'paginatedLinks' => $paginatedLinks,
            'typeId' => $typeId,
            'modelId' => $modelId,
            'searchQuery' => $searchQuery,
            'physicalStatus' => $physicalStatus,
            'transportStatus' => $transportStatus,
            'pspStatus' => $pspStatus,
            'deviceStatus' => $deviceStatus,
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $connectionTypes = DeviceConnectionType::orderBy('name', 'ASC')->get();
        $deviceTypes = DeviceType::where('status', 1)->orderBy('id', 'ASC')->get();
        $marketers = [];
        if ($user->isAdmin() || $user->isSuperuser()) {
            $marketers = User::where('level', 'AGENT')->where('parent_id', $user->id)->get();
        }
        return Inertia::render('Dashboard/Devices/CreateDevice', [
            'deviceConnectionTypes' => $connectionTypes,
            'deviceTypes' => $deviceTypes,
            'marketers' => $marketers,
        ]);
    }

    public function store(Request $request)
    {
        $request->validateWithBag('deviceForm', [
            'device_connection_type_id' => 'required|exists:device_connection_types,id',
            'device_type_id' => 'required|exists:device_types,id',
            'serial' => 'required|unique:devices,serial',
            'guarantee_start' => 'nullable|date',
            'guarantee_end' => 'nullable|date',
            'physical_status' => 'required',
            'transport_status' => 'required',
            'psp_status' => 'required',
        ]);

        Device::create($request->all());

        return redirect()->route('dashboard.devices.list');
    }

    public function view(Request $request)
    {
        $user = Auth::user();
        $deviceId = $request->route('id');
        $device = Device::with('deviceType')->find($deviceId);
        if (is_null($device)) return response()->json('not found', 404);

        $connectionTypes = DeviceConnectionType::orderBy('name', 'ASC')->get();
        $deviceTypes = DeviceType::where('status', 1)->orderBy('id', 'ASC')->get();
        $marketers = [];
        if ($user->isAdmin() || $user->isSuperuser()) {
            $marketers = User::where('level', 'AGENT')->where('parent_id', $user->id)->get();
        }
        return Inertia::render('Dashboard/Devices/EditDevice', [
            'device' => $device,
            'deviceTypes' => $deviceTypes,
            'deviceConnectionTypes' => $connectionTypes,
            'marketers' => $marketers,
        ]);
    }

    public function update(Request $request)
    {
        $deviceId = $request->route('id');
        $device = Device::find($deviceId);
        if (is_null($device)) return response()->json('not found', 404);

        $request->validateWithBag('deviceForm', [
            'device_connection_type_id' => 'required|exists:device_connection_types,id',
            'device_type_id' => 'required|exists:device_types,id',
            'serial' => 'required|unique:devices,serial,' . $deviceId,
            'guarantee_start' => 'nullable|date',
            'guarantee_end' => 'nullable|date',
            'physical_status' => 'required',
            'transport_status' => 'required',
            'psp_status' => 'required',
        ]);

        $device->fill($request->all());
        $device->save();

        return redirect()->route('dashboard.devices.list');
    }

    public function destroy(Request $request)
    {
        $deviceId = $request->route('id');
        Device::destroy($deviceId);

        return redirect()->route('dashboard.devices.list');
    }

    public function downloadExcel(Request $request)
    {
        $user = Auth::user();
        $devicesQuery = Device::with('deviceType')
            ->with('deviceType.type')
            ->with('user');

        if ($user->isAgent() || $user->isAdmin()) {
            $devicesQuery->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        }
        if ($user->isAdmin()) {
            $devicesQuery->orWhereHas('user', function ($query) use ($user) {
                $query->where('parent_id', $user->id);
            });
        }

        $searchQuery = $request->query('searchQuery', null);
        if (!is_null($searchQuery)) {
            $devicesQuery->where(function ($query) use ($searchQuery) {
                $query->where('serial', 'LIKE', '%' . $searchQuery . '%');
            });
        }

        $modelId = $request->query('modelId', '');
        if ($modelId != '') {
            $devicesQuery->where(function ($query) use ($modelId) {
                $query->where('device_type_id', $modelId);
            });
        }

        $typeId = $request->query('typeId', '');
        if ($typeId != '') {
            $deviceTypes = DeviceType::where('device_connection_type_id', $typeId)->pluck('id');
            $devicesQuery->whereIn('device_type_id', $deviceTypes);
        }

        $physicalStatus = $request->query('physicalStatus', '');
        if ($physicalStatus != '') {
            $devicesQuery->where(function ($query) use ($physicalStatus) {
                $query->where('physical_status', $physicalStatus);
            });
        }

        $transportStatus = $request->query('transportStatus', '');
        if ($transportStatus != '') {
            $devicesQuery->where(function ($query) use ($transportStatus) {
                $query->where('transport_status', $transportStatus);
            });
        }

        $pspStatus = $request->query('pspStatus', '');
        if ($pspStatus != '') {
            $devicesQuery->where(function ($query) use ($pspStatus) {
                $query->where('psp_status', $pspStatus);
            });
        }

        $devices = $devicesQuery->orderBy('id', 'ASC')->get();
        $jDate = Jalalian::forge(now())->format('Y.m.d');

        return Excel::download(new DeviceExport($devices), 'devices.' . $jDate . '.xlsx');
    }

    public function uploadExcel(Request $request)
    {
        $request->validateWithBag('excelForm', [
            'file' => 'required|file'
        ]);
        $user = Auth::user();
        $file = $request->file('file')->store('temp/excel/devices');
        Excel::import(new DeviceImport($user), $file);

        return redirect()->route('dashboard.devices.list');
    }

    public function getDeviceTypesListByAjax(Request $request)
    {
        $profileId = $request->route('profileId');
        $profile = Profile::find($profileId);
        if (is_null($profile)) return response()->json(['message' => 'اطلاعات پرونده یافت نشد'], 404);

        $deviceTypes = DeviceType::where(function () {

        })
            ->where('device_connection_type_id', $profile->deviceType->device_connection_type_id)
            ->where('status', 1)
            ->orderBy('id', 'ASC')->get();

        return response()->json($deviceTypes);
    }

    public function getDevicesListByAjax(Request $request)
    {
        $profileId = $request->route('profileId');
        $profile = Profile::find($profileId);
        $user = Auth::user();
        $devices = $this->getDeviceListByType($profile->device_type_id, $user);
        return response()->json($devices);
    }

    public function getNewDevicesListByAjax(Request $request)
    {
        $profileId = $request->route('profileId');
        $profile = Profile::find($profileId);

        $user = Auth::user();
        $devices = $this->getDeviceListByType($profile->new_device_type_id, $user);

        return response()->json($devices);
    }

    private function getDeviceListByType(int $typeId, $user)
    {
        $devices = Device::where('device_type_id', $typeId)
            ->where(function ($query) use ($user) {
                if ($user->isAdmin()) {
                    $query->where('user_id', $user->id);
                    $query->orWhereHas('user', function ($userQuery) use ($user) {
                        $userQuery->where('parent_id', $user->id);
                    });
                } elseif ($user->isAgent()) {
                    $query->where('user_id', $user->id);
                    $query->orWhere('user_id', $user->parent_id);
                }
            })
            ->where('physical_status', 1)
            ->where('transport_status', 1)
            ->where('psp_status', 1)
            ->where('status', 2)
            ->get();

        return $devices;
    }
}
