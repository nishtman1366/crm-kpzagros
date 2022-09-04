<?php

namespace App\Http\Controllers;

use App\Exports\Devices\DeviceExport;
use App\Http\Controllers\Notifications\NotificationController;
use App\Imports\Devices\DeviceImport;
use App\Models\Profiles\Profile;
use App\Models\Profiles\Terminal;
use App\Models\User;
use App\Models\Variables\Device;
use App\Models\Variables\DeviceConnectionType;
use App\Models\Variables\DeviceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\RequiredIf;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        $ownerId = $request->query('ownerId', '');
        if ($ownerId != '') {
            $devicesQuery->where(function ($query) use ($ownerId) {
                $query->where('user_id', $ownerId);
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

        $devices = $devicesQuery->orderBy('id', 'ASC')
            ->paginate();
        $paginatedLinks = paginationLinks($devices->appends($request->query->all()));

        $ownerIds = Device::groupBy('user_id')->get('user_id')->pluck('user_id');
        $owners = User::whereIn('id', $ownerIds)->get();

        $models = DeviceType::orderBy('name', 'ASC')->get();

        $types = DeviceConnectionType::orderBy('id', 'ASC')->get();

        return Inertia::render('Dashboard/Devices/DevicesList', [
            'devices' => $devices,
            'models' => $models,
            'owners' => $owners,
            'types' => $types,
            'paginatedLinks' => $paginatedLinks,
            'typeId' => $typeId,
            'modelId' => $modelId,
            'ownerId' => $ownerId,
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
        if ($user->isAdmin() || $user->isSuperuser() || $user->isOffice()) {
            $id = $user->isOffice() ? $user->parent_id : $user->id;
            $marketers = User::where('level', 'AGENT')->where('parent_id', $id)->get();
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
            'imei' => [
                new RequiredIf(!in_array($request->get('device_connection_type_id'), [1, 4])),
                is_null($request->get('imei')) ? '' : new Unique('devices', 'imei')
            ],
            'physical_status' => 'required',
            'transport_status' => 'required',
            'psp_status' => 'required',
        ]);
        $user = Auth::user();
        $device = Device::create($request->all());
        NotificationController::handleProfileNotifications('DEVICES', $device, $user);

        return redirect()->route('dashboard.devices.list');
    }

    public function view(Device $device, Request $request)
    {
        $device->load('deviceType', 'deviceType.type');
        $user = Auth::user();

        $connectionTypes = DeviceConnectionType::orderBy('name', 'ASC')->get();
        $deviceTypes = DeviceType::where('status', 1)->orderBy('id', 'ASC')->get();
        $marketers = [];
        if ($user->isAdmin() || $user->isSuperuser() || $user->isOffice()) {
            $id = $user->isOffice() ? $user->parent_id : $user->id;
            $marketers = User::where('level', 'AGENT')->where('parent_id', $id)->get();
        }
        if ($request->wantsJson()) return response()->json($device);
        return Inertia::render('Dashboard/Devices/EditDevice', [
            'device' => $device,
            'deviceTypes' => $deviceTypes,
            'deviceConnectionTypes' => $connectionTypes,
            'marketers' => $marketers,
        ]);
    }

    public function update(Device $device, Request $request)
    {
        $user = Auth::user();

        if ($device->status == 2 && $user->isAgent()) throw ValidationException::withMessages(['error' => 'پس از تایید دستگاه امکان ویرایش اطلاعات آن وجود ندارد.'])->errorBag('deviceForm');

        $request->validateWithBag('deviceForm', [
            'device_connection_type_id' => 'required|exists:device_connection_types,id',
            'device_type_id' => 'required|exists:device_types,id',
            'serial' => 'required|unique:devices,serial,' . $device->id,
            'imei' => [
                new RequiredIf(!in_array($request->get('device_connection_type_id'), [1, 4])),
                is_null($request->get('imei')) ? '' : (new Unique('devices', 'imei'))->ignoreModel($device)
            ],
            'physical_status' => 'required',
            'transport_status' => 'required',
            'psp_status' => 'required',
        ]);


        $requestStatus = $request->get('status');
        $oldStatus = $device->status;
        $device->fill($request->all());
        $device->save();
        if ($requestStatus != $oldStatus) {
            NotificationController::handleProfileNotifications('DEVICES', $device, $user);
        }

        return redirect()->route('dashboard.devices.list');
    }

    public function destroy(Device $device)
    {
        $user = Auth::user();
        if ($user->isAgent()) {
            if ($device->status == 2) throw new NotFoundHttpException('شما اجازه حذف این دستگاه را ندارید.');
        }
        if ($device->transport_status == 2) throw new NotFoundHttpException('دستگاه مورد نظر در انتظار نصب می باشد و شما نمیتوانید آنرا حذف کنید.');
        if ($device->transport_status == 3) throw new NotFoundHttpException('دستگاه مورد نظر در محل پذیرنده نصب می باشد و شما نمیتوانید آنرا حذف کنید.');
        if ($device->psp_status == 2) throw new NotFoundHttpException('دستگاه مورد نظر به پذیرنده اختصاص یافته است و شما نمیتوانید آنرا حذف کنید.');

        $device->delete();

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

    public function getDeviceTypesListByAjax(Terminal $terminal)
    {
        $deviceTypes = DeviceType::where(function () {

        })
            ->where('device_connection_type_id', $terminal->device_connection_type_id)
            ->where('status', 1)
            ->orderBy('id', 'ASC')->get();

        return response()->json($deviceTypes);
    }

    public function getDevicesListByAjax(Terminal $terminal, Request $request)
    {
        $user = Auth::user();
        $changeSerial = $request->query('change', false);
        $search = $terminal->device_type_id;
        if ($changeSerial) $search = $terminal->new_device_type_id;
        $devices = $this->getDeviceListByType($search, $user);
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
        $devices = Device::with('deviceType')
            ->with('deviceType.type')
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
//            ->limit(5)
            ->get();

        return $devices;
    }

    public function batchJob(Request $request)
    {
        $request->validateWithBag('batchJobs', [
            'type' => 'required',
            'list' => 'required|array'
        ],
            [
                'type.required' => 'لطفا نوع عملیات گروهی را انتخاب نمایید.'
            ]);

        $batchJobType = $request->get('type');
        $list = $request->get('list');
        switch ($batchJobType) {
            case 'changeOwner':
                $request->validateWithBag('batchJobs', [
                    'owner_id' => 'required|exists:users,id'
                ],
                    [
                        'owner_id.required' => 'لطفا مالک جدید دستگاه ها را انتخاب نمایید.'
                    ]);
                $ownerId = $request->get('owner_id');
                Device::whereIn('id', $list)->update(['user_id' => $ownerId]);
                break;
            case 'changeStatus':
                $request->validateWithBag('batchJobs', [
                    'device_status' => 'required'
                ],
                    [
                        'device_status.required' => 'لطفا وضعیت جدید دستگاه ها را انتخاب نمایید.'
                    ]);
                $status = $request->get('device_status');
                Device::whereIn('id', $list)->update(['status' => $status]);
                break;
            case 'changePhysicalStatus':
                $request->validateWithBag('batchJobs', [
                    'physical_status' => 'required'
                ],
                    [
                        'physical_status.required' => 'لطفا وضعیت فیزیکی جدید دستگاه ها را انتخاب نمایید.'
                    ]);
                $status = $request->get('physical_status');
                Device::whereIn('id', $list)->update(['physical_status' => $status]);
                break;
            case 'changeTransportStatus':
                $request->validateWithBag('batchJobs', [
                    'transport_status' => 'required'
                ],
                    [
                        'transport_status.required' => 'لطفا وضعیت انبار جدید دستگاه ها را انتخاب نمایید.'
                    ]);
                $status = $request->get('transport_status');
                Device::whereIn('id', $list)->update(['transport_status' => $status]);
                break;
            case 'changePspStatus':
                $request->validateWithBag('batchJobs', [
                    'psp_status' => 'required'
                ],
                    [
                        'psp_status.required' => 'لطفا وضعیت سرویس دهنده جدید دستگاه ها را انتخاب نمایید.'
                    ]);
                $status = $request->get('psp_status');
                Device::whereIn('id', $list)->update(['psp_status' => $status]);
                break;
        }

        return redirect()->route('dashboard.devices.list')->with('message', 'عملیات گروهی با موفقیت انجام شد.');
    }

    public function deviceTypes(DeviceType $type)
    {
        return response()->json($type);
    }
}
