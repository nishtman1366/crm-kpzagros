<?php

namespace App\Http\Controllers\Profiles;

use App\Exceptions\NotificationException;
use App\Exports\Profiles\ProfileExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Notifications\NotificationController;
use App\Imports\Profiles\ProfileImport;
use App\Models\Profiles\Profile;
use App\Models\Profiles\LicenseType;
use App\Models\Profiles\ProfileMessage;
use App\Models\User;
use App\Models\Variables\Device;
use App\Models\Variables\DeviceType;
use App\Models\Variables\Psp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $profilesQuery = Profile::with('customer')
            ->with('psp')
            ->with('user')
            ->with('user.parent')
            ->with('customer')
            ->whereHas('customer')
            ->where(function ($query) use ($user) {
                if (!$user->isSuperuser()) {
                    $query->where('user_id', $user->id);

                    if ($user->isAgent() || $user->isAdmin()) {
                        $query->orWhereHas('user', function ($query) use ($user) {
                            $query->where('parent_id', $user->id);
                        });
                    }

                    if ($user->isAdmin()) {
                        $query->orWhereHas('user.parent', function ($query) use ($user) {
                            $query->where('parent_id', $user->id);
                        });
                    }
                }
            });

        $pspId = $request->query('pspId', null);
        if (!is_null($pspId)) {
            $profilesQuery->where(function ($query) use ($pspId) {
                $query->where('psp_id', $pspId);
            });
        }

        $statusId = $request->query('statusId', null);
        if (!is_null($statusId)) {
            $profilesQuery->where(function ($query) use ($statusId) {
                $query->where('status', $statusId);
            });
        }

        $marketers = [];
        $agentId = $request->query('agentId', null);
        if (!is_null($agentId)) {
            $profilesQuery->where(function ($query) use ($agentId) {
                $query->where('user_id', $agentId);
            });

            $marketers = User::where('level', 'MARKETER')->where('parent_id', $agentId)->get();
        }

        $marketerId = $request->query('marketerId', null);
        if (!is_null($marketerId)) {
            $profilesQuery->where(function ($query) use ($marketerId) {
                $query->where('user_id', $marketerId);
            });
        }

        $searchQuery = $request->query('query', null);
        if (!is_null($searchQuery)) {
            $profilesQuery->whereHas('customer', function ($query) use ($searchQuery) {
                $query->where('national_code', 'LIKE', '%' . $searchQuery . '%');
                $query->orWhere('first_name', 'LIKE', '%' . $searchQuery . '%');
                $query->orWhere('last_name', 'LIKE', '%' . $searchQuery . '%');
            });
        }

        $fromDate = $request->query('fromDate', Jalalian::now()->subDays(7)->format('Y-m-d'));
        $fromDate = str_replace('/', '-', $fromDate);
        $jFromDate = $fromDate;
        $fromDate = Jalalian::fromFormat('Y-m-d', $fromDate)->toCarbon()->hour(0)->minute(0)->second(0);
        $profilesQuery->where('updated_at', '>=', $fromDate);

        $toDate = $request->query('toDate', Jalalian::now()->format('Y-m-d'));
        $toDate = str_replace('/', '-', $toDate);
        $jToDate = $toDate;
        $toDate = Jalalian::fromFormat('Y-m-d', $toDate)->toCarbon()->hour(23)->minute(59)->second(59);
        $profilesQuery->where('updated_at', '<=', $toDate);

        $profiles = $profilesQuery->orderBy('updated_at', 'DESC')
            ->paginate(30);

        $paginatedLinks = paginationLinks($profiles->appends($request->query->all()));


        /*
         * متغیرهای مورد نیاز
         */
        $statuses = [
            ['id' => 0, 'name' => 'ثبت موقت'],
            ['id' => 1, 'name' => 'ثبت شده'],
            ['id' => 2, 'name' => 'در انتظار بررسی مدارک'],
            ['id' => 3, 'name' => 'تایید مدارک'],
            ['id' => 4, 'name' => 'ثبت در PSP'],
            ['id' => 5, 'name' => 'تایید شاپرک'],
            ['id' => 6, 'name' => 'در انتظار تخصیص'],
            ['id' => 7, 'name' => 'تخصیص داده شده'],
            ['id' => 8, 'name' => 'نصب شده'],
            ['id' => 9, 'name' => 'ابطال'],
            ['id' => 10, 'name' => 'عدم تایید مدارک'],
            ['id' => 11, 'name' => 'عدم تایید شاپرک'],
            ['id' => 12, 'name' => 'درخواست ابطال'],
            ['id' => 13, 'name' => 'عدم تایید سریال'],
            ['id' => 14, 'name' => 'درخواست جابجایی'],
            ['id' => 15, 'name' => 'اختصاص سریال جدید'],
            ['id' => 16, 'name' => 'رد درخواست جابجایی'],
        ];

        $psps = Psp::where('status', 1)->orderBy('name', 'ASC')->get();

        $agents = [];
        if ($user->isAdmin() || $user->isSuperuser()) {
            $agents = User::where('level', 'AGENT')->where(function ($query) use ($user) {
                if ($user->isAdmin()) {
                    $query->where('parent_id', $user->id);
                }
            })->get();
        }

        if ($user->isAgent()) {
            $marketers = User::where('level', 'MARKETER')->where('parent_id', $user->id)->get();
        }


//        dd([$fromDate, $toDate]);
        return Inertia::render('Dashboard/Profiles/ProfilesList',
            [
                'profiles' => $profiles,

                'psps' => $psps,
                'pspId' => $pspId,

                'statusId' => $statusId,
                'statuses' => $statuses,

                'agents' => $agents,
                'agentId' => $agentId,

                'marketers' => $marketers,
                'marketerId' => $marketerId,

                'searchQuery' => $searchQuery,

                'fromDate' => $jFromDate,
                'toDate' => $jToDate,

                'paginatedLinks' => $paginatedLinks,
            ]
        );
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $profile = Profile::create(['user_id' => $user->id]);

        return redirect()->route('dashboard.profiles.customers.create', ['profileId' => $profile->id]);
    }

    public function view(Request $request)
    {
        $user = Auth::user();
        $profileId = $request->route('profileId');
        $profile = Profile::with('customer')
            ->with('psp')
            ->with('business')
            ->with('accounts')
            ->with('accounts.account')
            ->with('accounts.account.bank')
            ->with('deviceType')
            ->with('deviceType.type')
            ->with('device')
            ->with('messages')
            ->with('licenses')
            ->with('licenses.type')
            ->find($profileId);

        if (is_null($profile)) return response()->json(['message' => 'اطلاعات پرونده یافت نشد'], 404);
        if (is_null($profile->customer)) return response()->json(['message' => 'اطلاعات مشتری یافت نشد'], 404);
        if (is_null($profile->business)) return redirect()->route('dashboard.profiles.businesses.create', ['profileId' => $profileId]);
        if (count($profile->accounts) == 0) return redirect()->route('dashboard.profiles.accounts.create', ['profileId' => $profileId]);
        if (is_null($profile->deviceType)) return redirect()->route('dashboard.profiles.devices.create', ['profileId' => $profileId]);


        $psps = Psp::where('status', 1)->orderBy('name', 'ASC')->get();
        $licenseTypes = LicenseType::where('status', 1)->orderBy('name', 'ASC')->get();
        return Inertia::render('Dashboard/Profiles/ViewProfile', [
            'profile' => $profile,
            'psps' => $psps,
            'licenseTypes' => $licenseTypes,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $profileId = $request->route('profileId');
        $profile = Profile::with('customer')
            ->with('business')
            ->with('accounts')
            ->with('accounts.account')
            ->with('accounts.account.bank')
            ->find($profileId);
        if (is_null($profile)) return response()->json(['message' => 'اطلاعات پرونده یافت نشد'], 404);

        $status = $request->get('status');

        if ($status == 1) {
            $errors = LicenseController::checkProfileLicenses($profile);
            if (count($errors) > 0) {
                return redirect()->route('dashboard.profiles.view', ['profileId' => $profileId])->withErrors($errors);
            }
        }

        if ($status === 10) {
            $request->validateWithBag('profileForm', [
                'title' => 'nullable',
                'message' => 'required'
            ]);
        }

        if ($status === 8) {
            $device = Device::find($profile->device_id);
            if (is_null($device)) throw new NotFoundHttpException('اطلاعات دستگاه یافت نشد.');
            $device->update(['transport_status' => 3]);
        }

        $profile->fill($request->all());
        $profile->save();

        $this->setProfileMessage($status, $user, $profile, $request->get('message', null));

        if ($status == 2 || $status == 4) return back()->with(['message' => 'پرونده در لیست در حال بررسی قرار گرفت']);

        return redirect()->route('dashboard.profiles.view', ['profileId' => $profileId]);
    }

    public function updateSerial(Request $request)
    {
        $user = Auth::user();
        $profileId = $request->route('profileId');
        $profile = Profile::find($profileId);
        if (is_null($profile)) return response()->json(['message' => 'اطلاعات پرونده یافت نشد'], 404);
        $deviceId = $request->get('device_id');

        $profile->fill([
            'device_id' => $deviceId,
            'status' => 6
        ]);

        $profile->save();

        Device::find($deviceId)->update([
            'transport_status' => 2
        ]);

        $this->setProfileMessage(6, $user, $profile, null);

        return redirect()->route('dashboard.profiles.list')->with(['message' => 'درخواست ثبت شماره سریال با موفقیت ثبت شد.']);

    }

    public function updateTerminal(Request $request)
    {
        $request->validateWithBag('terminalForm', [
            'terminal_id' => 'required',
            'merchant_id' => 'required'
        ]);

        $user = Auth::user();
        $profileId = $request->route('profileId');

        $profile = Profile::find($profileId);
        if (is_null($profile)) throw new NotFoundHttpException('اطلاعات پرونده یافت نشد.');

        $device = Device::find($profile->device_id);
        if (is_null($device)) throw new NotFoundHttpException('اطلاعات دستگاه یافت نشد.');
        $device->update(['psp_status' => 2]);

        $request->merge(['status' => 7]);
        $profile->fill($request->all());

        $profile->save();

        $this->setProfileMessage(7, $user, $profile, null);

        return redirect()->route('dashboard.profiles.list')->with(['message' => 'درخواست ثبت شماره ترمینال و شماره پذیرنده با موفقیت ثبت شد.']);

    }

    public function cancelRequest(Request $request)
    {
        $request->validateWithBag('cancelRequestForm', [
            'cancel_reason' => 'required',
        ]);

        $user = Auth::user();
        $profileId = $request->route('profileId');
        $profile = Profile::find($profileId);
        if (is_null($profile)) return response()->json(['message' => 'اطلاعات پرونده یافت نشد'], 404);

        $request->merge(['status' => 12]);
        $profile->fill($request->all());

        $profile->save();

        $this->setProfileMessage(12, $user, $profile, null);

        return redirect()->route('dashboard.profiles.list')->with(['message' => 'درخواست فسخ پرونده با موفقیت ثبت شد.']);
    }

    public function cancelConfirm(Request $request)
    {
        $cancelType = $request->get('confirmCancelMessage');
        $status = 9;
        if ($cancelType) {
            $request->validateWithBag('confirmCancelForm', [
                'message' => 'required',
            ]);
            $status = 8;
        }

        $user = Auth::user();
        $profileId = $request->route('profileId');
        $profile = Profile::find($profileId);
        if (is_null($profile)) return response()->json(['message' => 'اطلاعات پرونده یافت نشد'], 404);

        $profile->fill(['status' => $status]);

        $profile->save();

        $this->setProfileMessage($status == 8 ? 14 : $status, $user, $profile, $request->get('message'));

        return redirect()->route('dashboard.profiles.list')->with(['message' => 'نتیجه فسخ پرونده با موفقیت ثبت شد.']);
    }

    public function changeRequest(Request $request)
    {
        $request->validateWithBag('changeSerialRequestForm', [
            'change_reason' => 'required',
            'new_device_type_id' => 'required',
        ]);

        $user = Auth::user();
        $profileId = $request->route('profileId');
        $profile = Profile::find($profileId);
        if (is_null($profile)) return response()->json(['message' => 'اطلاعات پرونده یافت نشد'], 404);

        $request->merge(['status' => 14]);
        $profile->fill($request->all());

        $profile->save();

        $this->setProfileMessage(14, $user, $profile, null);

        return redirect()->route('dashboard.profiles.list')->with(['message' => 'درخواست جابجایی سریال با موفقیت ثبت شد.']);
    }

    public function getNewDeviceByAjax(Request $request)
    {
        $profileId = $request->route('profileId');
        $profile = Profile::find($profileId);
        if (is_null($profile)) return response()->json(['message' => 'اطلاعات پرونده یافت نشد'], 404);
        $device = Device::with('deviceType')
            ->where('id', $profile->new_device_id)
            ->get()
            ->first();

        return response()->json($device);
    }

    public function getNewDeviceTypeByAjax(Request $request)
    {
        $profileId = $request->route('profileId');
        $profile = Profile::find($profileId);
        if (is_null($profile)) return response()->json(['message' => 'اطلاعات پرونده یافت نشد'], 404);
        $deviceType = DeviceType::with('type')->where('id', $profile->new_device_type_id)->get()->first();

        return response()->json($deviceType);
    }

    public function newSerial(Request $request)
    {
        $request->validateWithBag('selectNewSerialForm', [
            'new_device_id' => 'required',
        ]);

        $user = Auth::user();
        $profileId = $request->route('profileId');
        $profile = Profile::find($profileId);
        if (is_null($profile)) return response()->json(['message' => 'اطلاعات پرونده یافت نشد'], 404);

        $profile->fill(['new_device_id' => $request->get('new_device_id'), 'status' => 15]);

        $profile->save();

        $this->setProfileMessage(15, $user, $profile, null);

        return redirect()->route('dashboard.profiles.list')->with(['message' => 'سریال جدید با موفقیت ثبت شد.']);
    }

    public function changeConfirm(Request $request)
    {
        $cancelType = $request->get('confirmChangeMessage');
        $status = 7;
        if ($cancelType) {
            $request->validateWithBag('confirmChangeSerialForm', [
                'change_message' => 'required',
            ]);
            $status = 15;
        }

        $user = Auth::user();
        $profileId = $request->route('profileId');
        $profile = Profile::with('customer')->find($profileId);
        if (is_null($profile)) return response()->json(['message' => 'اطلاعات پرونده یافت نشد'], 404);

        $oldDevice = Device::find($profile->device_id);
        if (!is_null($oldDevice)) {
            $oldDevice->transport_status = 1;
            $oldDevice->psp_status = 1;
            $oldDevice->save();
        }

        $newDevice = Device::find($profile->new_device_id);
        if (!is_null($newDevice)) {
            $newDevice->transport_status = 2;
            $newDevice->psp_status = 2;
            $newDevice->save();
        }

        if ($status == 7) $request->merge(['device_type_id' => $profile->new_device_type_id, 'device_id' => $profile->new_device_id]);
        $profile->fill(['status' => $status]);

        $profile->save();

        $this->setProfileMessage($status == 15 ? 15 : 17, $user, $profile, $request->get('change_message'));

        return redirect()->route('dashboard.profiles.list')->with(['message' => 'نتیجه جابجایی سریال با موفقیت ثبت شد.']);
    }

    private function setProfileMessage($status, $user, $profile, $message = null)
    {
        switch ($status) {
            default:
            case 1:
                $title = sprintf('ثبت اطلاعات پرونده توسط %s انجام شد.', $user->name);
                $type = 'SUCCESS';
                break;
            case 2:
                $title = sprintf('پرونده توسط %s در انتظار بررسی مدارک می باشد.', $user->name);
                $type = 'INFO';
                break;
            case 3:
                $title = sprintf('مدارک پرونده توسط %s تایید شد و هم اکنون در حال ثبت در سامانه خدمات دهنده (psp) می باشد.', $user->name);
                $type = 'INFO';
                break;
            case 4:
                $title = sprintf('اطلاعات پرونده توسط %s در سامانه خدمات دهنده (psp) ثبت شد.', $user->name);
                $type = 'SUCCESS';
                break;
            case 5:
                $title = sprintf('اطلاعات پرونده توسط شاپرک تایید شد.');
                $type = 'SUCCESS';
                break;
            case 6:
                $title = sprintf('پرونده در انتظار تخصیص شماره پذیرنده و شماره ترمینال می باشد.');
                $type = 'WARNING';
                break;
            case 7:
                $title = sprintf('شماره پذیرنده و شماره ترمینال به پرونده اختصاص داده شد. لطفا جهت نصب دستگاه اقدام نمایید.');
                $type = 'SUCCESS';
                break;
            case 8:
                $title = sprintf('دستگاه توسط %s در محل مشتری با موفقیت نصب شد.', $user->name);
                $type = 'SUCCESS';
                break;
            case 9:
                $title = sprintf('پرونده توسط %s ابطال شد.', $user->name);
                $type = 'SUCCESS';
                break;
            case 10:
                $title = sprintf('مدارک پرونده توسط %s رد شد.', $user->name);
                $type = 'DANGER';
                break;
            case 11:
                $title = sprintf('اطلاعات پرونده توسط شاپرک رد شد.');
                $type = 'DANGER';
                break;
            case 12:
                $title = sprintf('درخواست فسخ پرونده توسط %s ثبت شد.', $user->name);
                $type = 'WARNING';
                break;
            case 13:
                $title = sprintf('سریال انتخاب شده برای پرونده توسط %s رد شد.', $user->name);
                $type = 'DANGER';
                break;
            case 14:
                $title = sprintf('درخواست جابجایی پرونده توسط %s ثبت شد.', $user->name);
                $type = 'WARNING';
                break;
            case 15:
                $title = sprintf('سریال جدید توسط %s جهت جابجایی انتخاب شد.', $user->name);
                $type = 'WARNING';
                break;
            case 16:
                $title = sprintf('درخواست جابجایی پرونده توسط %s رد شد.', $user->name);
                $type = 'DANGER';
                break;
            case 17:
                $title = 'سریال جدید به پذیرنده اختصاص یافت.';
                $type = 'INFO';
                break;
            case 18:
                $title = sprintf('درخواست فسخ پرونده توسط %s رد شد.', $user->name);
                $type = 'DANGER';
                break;
        }


        ProfileMessage::create([
            'user_id' => $user->id,
            'profile_id' => $profile->id,
            'message' => $message,
            'title' => $title,
            'type' => $type
        ]);

        NotificationController::handleProfileNotifications('PROFILE', $profile, $user);

    }

    public function downloadExcel(Request $request)
    {
        $user = Auth::user();
        $profilesQuery = Profile::with('customer')
            ->withCount('accounts')
            ->with('user')
            ->with('user.parent')
            ->with('customer')
            ->whereHas('customer')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                if ($user->isAgent() || $user->isAdmin() || $user->isSuperUser()) {
                    $query->orWhereHas('user', function ($query) use ($user) {
                        $query->where('parent_id', $user->id);
                    });
                }

                if ($user->isAdmin() || $user->isSuperUser()) {
                    $query->orWhereHas('user.parent', function ($query) use ($user) {
                        $query->where('parent_id', $user->id);
                    });
                }
            });

        $statusId = $request->query('statusId', null);
        if (!is_null($statusId)) {
            $profilesQuery->where(function ($query) use ($statusId) {
                $query->where('status', $statusId);
            });
        } else {
            $profilesQuery->where('status', '>=', 1);
        }

        $agentId = $request->query('agentId', null);
        if (!is_null($agentId)) {
            $profilesQuery->where(function ($query) use ($agentId) {
                $query->where('user_id', $agentId);
            });
        }

        $marketerId = $request->query('marketerId', null);
        if (!is_null($marketerId)) {
            $profilesQuery->where(function ($query) use ($marketerId) {
                $query->where('user_id', $marketerId);
            });
        }

        $searchQuery = $request->query('query', null);
//        if()

        $profiles = $profilesQuery->orderBy('id', 'ASC')->get();

        $jDate = Jalalian::forge(now())->format('Y.m.d');
        return Excel::download(new ProfileExport($profiles), 'profiles.' . $jDate . '.xlsx');
    }

    public function uploadExcel(Request $request)
    {
        $user = Auth::user();
        $request->validateWithBag('uploadExcelForm', [
            'file' => 'required|file'
        ]);
        $file = $request->file('file')->store('temp/excel/profiles');
        Excel::import(new ProfileImport($user), $file);
        return redirect()->route('dashboard.profiles.list');
    }
}
