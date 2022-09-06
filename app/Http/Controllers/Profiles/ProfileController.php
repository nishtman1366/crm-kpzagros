<?php

namespace App\Http\Controllers\Profiles;

use App\Exports\Profiles\ProfileExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Requests\Profiles\Profile\UpdateTerminal;
use App\Imports\Profiles\CustomImport;
use App\Imports\Profiles\ProfileImport;
use App\Imports\Profiles\Psp\AirikPasargad;
use App\Jobs\Profiles\CreateZipArchive;
use App\Jobs\Profiles\DeleteExportedFiles;
use App\Jobs\Profiles\ExportProfiles;
use App\Models\Profiles\Profile;
use App\Models\Profiles\LicenseType;
use App\Models\Profiles\ProfileMessage;
use App\Models\User;
use App\Models\Variables\Device;
use App\Models\Variables\DevicePsp;
use App\Models\Variables\DeviceType;
use App\Models\Variables\Psp;
use App\Rules\ChangeSerial;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Morilog\Jalali\Jalalian;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

//        $userIdList = collect([]);
//        if ($user->isAdmin() || $user->isAgent() || $user->isMarketer() || $user->isOffice()) {
//            $userIdList->push($user->id);
//        }
//
//        if ($user->isAdmin() || $user->isAgent() || $user->isOffice()) {
//            if ($user->isOffice()) {
//                $userIdList->push($user->parent_id);
//                $childrenId = User::where('parent_id', $user->parent_id)->pluck('id');
//            } else {
//                $childrenId = User::where('parent_id', $user->id)->pluck('id');
//            }
//            $userIdList = $userIdList->merge($childrenId);
//        }
//
//        if ($user->isAdmin() || $user->isOffice()) {
//            if ($user->isOffice()) {
//                $parentsLis = User::where('parent_id', $user->parent_id)->pluck('id');
//            } else {
//                $parentsLis = User::where('parent_id', $user->id)->pluck('id');
//            }
//            $childrenId = User::whereIn('parent_id', $parentsLis)->pluck('id');
//            $userIdList = $userIdList->merge($childrenId);
//        }

//        DB::enableQueryLog();
        $profilesQuery = Profile::with('customer')
            ->with('psp')
            ->with('user')
            ->with('terminals')
            ->with('terminals.device')
            ->with('terminals.deviceType')
            ->with('terminals.deviceConnectionType')
            ->with('user.parent')
            ->with('customer')
            ->whereHas('customer')
            ->where(function ($query) use ($user) {
                if (!$user->isSuperuser()) {
                    $query->where('user_id', $user->id);

                    if ($user->isAgent() || $user->isAdmin() || $user->isOffice()) {
                        $id = $user->isOffice() ? $user->parent_id : $user->id;
                        $query->orWhereHas('user', function ($query) use ($id) {
                            $query->where('parent_id', $id);
                        });
                    }

                    if ($user->isAdmin() || $user->isOffice()) {
                        $id = $user->isOffice() ? $user->parent_id : $user->id;
                        $query->orWhereHas('user.parent', function ($query) use ($id) {
                            $query->where('parent_id', $id);
                        });
                    }

                    if ($user->isOffice()) {
                        $query->orWhere('user_id', $user->parent_id);
                    }
                }
            });
//        dd(DB::getQueryLog());


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

        $licenseStatus = $request->query('licenseStatus', null);
        if (!is_null($licenseStatus)) {
            $profilesQuery->where(function ($query) use ($licenseStatus) {
                $query->where('licenses_status', $licenseStatus);
            });
        }

//        $marketers = [];
        $agentId = $request->query('agentId', null);
//        if (!is_null($agentId)) {
//            $profilesQuery->where(function ($query) use ($agentId) {
//                $query->where('user_id', $agentId);
//            });
//
////            $marketers = User::where('level', 'MARKETER')->where('parent_id', $agentId)->get();
//        }
//

        $marketerId = $request->query('marketerId', null);

        if (!is_null($agentId)) {
            $profilesQuery->where(function ($query) use ($agentId, $marketerId) {
                if (is_null($marketerId)) {
                    $query->where('user_id', $agentId)
                        ->orWhereHas('user', function ($userQuery) use ($agentId) {
                            $userQuery->where('parent_id', $agentId);
                        });
                } else {
                    $query->orWhere('user_id', $marketerId);
                }
            });
        }

        if (is_null($agentId) && !is_null($marketerId)) {
            $profilesQuery->where(function ($query) use ($marketerId) {
                $query->where('user_id', $marketerId);
            });
        }

        $profileType = $request->query('profileType', null);
        if (!is_null($profileType)) {
            $profilesQuery->where(function ($query) use ($profileType) {
                $query->where('type', $profileType);
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
        $jFromDate = $jToDate = null;
        $fromDate = $request->query('fromDate');
        if ($fromDate) {
            $fromDate = str_replace('/', '-', $fromDate);
            $jFromDate = $fromDate;
            $fromDate = Jalalian::fromFormat('Y-m-d', $fromDate)->toCarbon()->hour(0)->minute(0)->second(0);
            $profilesQuery->where('updated_at', '>=', $fromDate);
        }
        $toDate = $request->query('toDate');
        if ($toDate) {
            $toDate = str_replace('/', '-', $toDate);
            $jToDate = $toDate;
            $toDate = Jalalian::fromFormat('Y-m-d', $toDate)->toCarbon()->hour(23)->minute(59)->second(59);
            $profilesQuery->where('updated_at', '<=', $toDate);
        }
        $profiles = $profilesQuery->orderBy('updated_at', 'DESC')
            ->paginate(30);

        $paginatedLinks = paginationLinks($profiles->appends($request->query->all()));
//        dd($profiles);

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
        if ($user->isAdmin() || $user->isSuperuser() || $user->isOffice()) {
            $id = $user->isOffice() ? $user->parent_id : $user->id;
            $agents = User::where('level', 'AGENT')->where(function ($query) use ($user, $id) {
                if ($user->isAdmin() || $user->isOffice()) {
                    $query->where('parent_id', $id);
                }
            })->get();
        }

        $marketers = User::where('level', 'MARKETER')
            ->where(function ($query) use ($agentId, $user) {
                if ($user->isAgent()) {
                    $query->where('parent_id', $user->id);
                } elseif ($user->isAdmin() || $user->isSuperuser() || $user->isOffice()) {
                    $id = $user->isOffice() ? $user->parent_id : $user->id;
                    $query->where('parent_id', $id);
                    if (!is_null($agentId)) {
                        $query->orWhere('parent_id', $agentId);
                    }
                }
            })
            ->get();

        if ($request->wantsJson()) return response()->json([
            'profiles' => $profiles,
            'psps' => $psps,
            'statuses' => $statuses
        ]);
//        dd([$fromDate, $toDate]);
        return Inertia::render('Dashboard/Profiles/ProfilesList',
            [
                'profiles' => $profiles,

                'psps' => $psps,
                'pspId' => $pspId,

                'statusId' => $statusId,
                'statuses' => $statuses,

                'licenseStatus' => $licenseStatus,

                'agents' => $agents,
                'agentId' => $agentId,

                'marketers' => $marketers,
                'marketerId' => $marketerId,

                'profileType' => $profileType,

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
        return redirect()->route('dashboard.profiles.customers.create', ['profile' => $profile->id]);
    }

    public function view(Profile $profile, Request $request)
    {
        $profile->load([
            'customer', 'psp',
            'terminals', 'terminals.deviceConnectionType', 'terminals.deviceType', 'terminals.device', 'terminals.device.deviceType',
            'business', 'business.category', 'business.subCategory',
            'accounts', 'accounts.account', 'accounts.account.bank',
            'messages', 'licenses', 'licenses.type'
        ]);

        if (is_null($profile->customer)) throw new NotFoundHttpException('اطلاعات مشتری یافت نشد');
        if (is_null($profile->business)) return redirect()->route('dashboard.profiles.businesses.create', ['profile' => $profile]);
        if (count($profile->accounts) == 0) return redirect()->route('dashboard.profiles.accounts.create', ['profile' => $profile]);
        if (count($profile->terminals) === 0) return redirect()->route('dashboard.profiles.devices.create', ['profile' => $profile]);

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

        $deviceTypes = DeviceType::where(function ($query) use ($profile) {
//            if (!is_null($profile->psp_id)) {
            $devicePsps = DevicePsp::where('psp_id', $profile->psp_id)->pluck('device_type_id');
            $query->whereIn('id', $devicePsps);
//            }
        })->get();

        $licenseTypes = LicenseType::where('status', 1)->orderBy('name', 'ASC')->get();

        return Inertia::render('Dashboard/Profiles/ViewProfile', [
            'profile' => $profile,
            'psps' => $psps,
            'statuses' => $statuses,
            'licenseTypes' => $licenseTypes,
            'deviceTypes' => $deviceTypes,
            'selectedTab' => $request->query('action', 'general'),
        ]);
    }

    public function update(Profile $profile, Request $request)
    {
        $user = Auth::user();
        $profile->load('customer', 'business', 'accounts', 'accounts.account', 'accounts.account.bank');

        $status = $request->get('status');

        if ($status == 1) {
            $errors = LicenseController::checkProfileLicenses($profile);
            if (count($errors) > 0) {
                return redirect()->route('dashboard.profiles.view', ['profile' => $profile->id])->withErrors($errors);
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

        return redirect()->route('dashboard.profiles.view', ['profile' => $profile->id]);
    }

    public function updateStatus(Profile $profile, Request $request)
    {
        $user = Auth::user();
        $newStatus = $request->get('newStatus');
        $profile->status = $newStatus;
        $profile->save();

        $this->setProfileMessage($newStatus, $user, $profile, 'تغییر وضعیت پرونده به صورت دستی');

        return redirect()->route('dashboard.profiles.view', ['profile' => $profile->id])->with(['message' => 'تغییر وضعیت پرونده صورت گرفت.']);
    }

    public function setType(Profile $profile, Request $request)
    {
        $user = Auth::user();

        $type = $request->get('type');
        if ($type == 'TRANSFER') {
            $request->validateWithBag('profileTypeForm', [
                'type' => 'required',
                'previous_name' => 'required',
                'previous_national_code' => 'required|numeric|digits:10',
                'previous_mobile' => 'required|numeric|digits:11',
            ]);
            if (!LicenseController::has('transfer_file', $profile->id)) {
                $request->validateWithBag('profileTypeForm', [
                    'transfer_file' => 'required|image',
                ]);
            }
            if (!LicenseController::has('transfer_payment_file', $profile->id)) {
                $request->validateWithBag('profileTypeForm', [
                    'transfer_payment_file' => 'required|image',
                ]);
            }
            $message = 'درخواست انتقال مالکیت با موفقیت ثبت شد.';
        } else {
            $message = 'تغییر نوع پرونده با موفقیت ثبت شد.';

            $request->merge([
                'previous_name' => null,
                'previous_national_code' => null,
                'previous_mobile' => null,
            ]);
        }

        $profile->fill($request->all());

        $profile->save();

        if ($request->hasFile('transfer_file') && $type == 'TRANSFER') {
            LicenseController::upload($request->file('transfer_file'), 'transfer_file', $profile->id);
        }

        if ($request->hasFile('transfer_payment_file') && $type == 'TRANSFER') {
            LicenseController::upload($request->file('transfer_payment_file'), 'transfer_payment_file', $profile->id);
        }
        return redirect()->route('dashboard.profiles.view', ['profile' => $profile->id])->with(['message' => $message]);

    }

    public function updateMerchant(Profile $profile, Request $request)
    {
        $request->validateWithBag('merchantForm', [
            'merchant_id' => 'required|unique:profiles,merchant_id'
        ]);

        $profile->merchant_id = $request->get('merchant_id');
        $profile->save();
        $user = Auth::user();
        $this->setProfileMessage('', $user, $profile);

        return redirect()->back();
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
            case 19:
                $title = sprintf('شماره پذیرنده توسط %s ثبت شد.', $user->name);
                $type = 'SUCCESS';
                break;
        }


        ProfileMessage::create([
            'user_id' => $user->id,
            'profile_id' => $profile->id,
            'message' => $message,
            'title' => $title,
            'type' => $type
        ]);

        NotificationController::handleProfileNotifications('PROFILES', $profile, $user);

    }

    public function downloadExcel(Request $request)
    {
        $user = Auth::user();
        $profilesQuery = Profile::with('customer')
            ->withCount('accounts')
            ->with('user')
            ->with('user.parent')
            ->whereHas('customer')
            ->where(function ($query) use ($user) {
                if (!$user->isSuperUser()) {
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

        $statusId = $request->query('statusId', null);
        if (!is_null($statusId)) {
            $profilesQuery->where(function ($query) use ($statusId) {
                $query->where('status', $statusId);
            });
        } else {
            $profilesQuery->where('status', '>=', 1);
        }

//        $marketers = [];
        $agentId = $request->query('agentId', null);
//        if (!is_null($agentId)) {
//            $profilesQuery->where(function ($query) use ($agentId) {
//                $query->where('user_id', $agentId);
//            });
//
////            $marketers = User::where('level', 'MARKETER')->where('parent_id', $agentId)->get();
//        }
//

        $marketerId = $request->query('marketerId', null);

        if (!is_null($agentId)) {
            $profilesQuery->where(function ($query) use ($agentId, $marketerId) {
                if (is_null($marketerId)) {
                    $query->where('user_id', $agentId)
                        ->orWhereHas('user', function ($userQuery) use ($agentId) {
                            $userQuery->where('parent_id', $agentId);
                        });
                } else {
                    $query->orWhere('user_id', $marketerId);
                }
            });
        }

        if (is_null($agentId) && !is_null($marketerId)) {
            $profilesQuery->where(function ($query) use ($marketerId) {
                $query->where('user_id', $marketerId);
            });
        }

        $profileType = $request->query('profileType', null);
        if (!is_null($profileType)) {
            $profilesQuery->where(function ($query) use ($profileType) {
                $query->where('type', $profileType);
            });
        }

        $licenseStatus = $request->query('licenseStatus');
        if (!is_null($licenseStatus)) {
            $profilesQuery->where(function ($query) use ($licenseStatus) {
                $query->where('licenses_status', $licenseStatus);
            });
        }

        $this->deleteExportProcess($user);

        $searchQuery = $request->query('query', null);

        $fromDate = $request->query('fromDate', null);
        if ($fromDate) {
            $fromDate = str_replace('/', '-', $fromDate);
            $fromDate = Jalalian::fromFormat('Y-m-d', $fromDate)->toCarbon()->hour(0)->minute(0)->second(0);
            $profilesQuery->where('updated_at', '>=', $fromDate);
        }
        $toDate = $request->query('toDate');
        if ($toDate) {
            $toDate = str_replace('/', '-', $toDate);
            $toDate = Jalalian::fromFormat('Y-m-d', $toDate)->toCarbon()->hour(23)->minute(59)->second(59);
            $profilesQuery->where('updated_at', '<=', $toDate);
        }
        $jDate = Jalalian::forge(now())->format('Y.m.d');
        $i = 1;
        $excelJobList = collect();
        $profilesQuery->orderBy('id', 'ASC')->chunk(999, function ($profiles) use (&$excelJobList, $jDate, &$i, $user) {
            $excelJobList->push(new ExportProfiles($profiles, $user));
            $i++;
        });
        Cache::put(sprintf('%s.profiles.export.total', $user->id), $i);
        $excelJobList->push(new CreateZipArchive($user));
        $excelJobList->push(new DeleteExportedFiles($user));
        Bus::chain($excelJobList->toArray())
            ->onQueue('ExportQueue')
            ->dispatch();
        return redirect()->back();
    }

    public function excelStatus(Request $request)
    {
        sleep(1);
        $user = Auth::user();
        $status = Cache::get(sprintf('%s.profiles.export.status', $user->id));
        if (!is_null($status)) {
            $response = [
                'status' => $status
            ];
            if ($status === 'processing') {
                $total = (int)Cache::get(sprintf('%s.profiles.export.total', $user->id));
                $done = (int)Cache::get(sprintf('%s.profiles.export.done', $user->id));
                $response['message'] = 'فرایند تبدیل فایل در حال پردازش است.';
                $response['complete'] = round(($done / $total) * 100, 1);
            } elseif ($status === 'failed') {
                $response['message'] = 'آخرین فرایند تبدیل فایل با شکست مواجه شده است.';
            } elseif ($status === 'done') {
                $response['message'] = 'آخرین فرایند تبدیل فایل با موفقیت به پایان رسید.';
                $expiration = Cache::get(sprintf('%s.profiles.export.expiration', $user->id));
                if (!is_null($expiration)) {
                    $response['expiration'] = Jalalian::forge($expiration)->format('Y/m/d H:i');
                }
                $response['url'] = Cache::get(sprintf('%s.profiles.export.zipFileUrl', $user->id));
            }
            return response()->json($response);
        }

        return response()->json([
            'status' => 'NotFound'
        ]);
    }

    private function deleteExportProcess($user)
    {
        $zipFile = Cache::get(sprintf('%s.profiles.export.zipFile', $user->id));
        if (!is_null($zipFile)) {
            Storage::disk('public')->delete(sprintf('archives/%s', $zipFile));
        }
        $directory = Cache::get(sprintf('%s.profiles.export.directory', $user->id));
        Cache::forget(sprintf('%s.profiles.export.status', $user->id));
        Cache::forget(sprintf('%s.profiles.export.directory', $user->id));
        Cache::forget(sprintf('%s.profiles.export.zipFileUrl', $user->id));
        Cache::forget(sprintf('%s.profiles.export.zipFile', $user->id));
        Cache::forget(sprintf('%s.profiles.export.done', $user->id));
        Cache::forget(sprintf('%s.profiles.export.total', $user->id));
        Cache::forget(sprintf('%s.profiles.export.expiration', $user->id));
        if ($directory) {
            Storage::deleteDirectory('temp/excel/profiles/' . $directory);
        }
    }

    public function cancelExportJob(Request $request)
    {
        $user = Auth::user();
        $this->deleteExportProcess($user);
        return response()->json([
            'status' => 'NotFound',
            'message' => 'حذف فایل‌ها با موفقیت انجام شد.'
        ]);
    }

    public function uploadExcel(Request $request)
    {
        $user = Auth::user();
        $request->validateWithBag('uploadExcelForm', [
            'file' => 'required|file',
            'psp_id' => 'required|exists:psps,id'
        ]);
        $importer = null;
        switch ($request->get('psp_id')) {
            case 14:
                $importer = new AirikPasargad();
                break;
        }
        if (!is_null($importer)) {
            $file = $request->file('file')->store('temp/excel/profiles');
            Excel::import($importer, $file);
        }
        return redirect()->route('dashboard.profiles.list');
    }

    public function confirmLicenses(Profile $profile, Request $request)
    {
        $profileId = (int)$request->route('profileId');

        $status = $request->get('status');
        $message = $request->get('message');

        $profile->licenses_status = $status;
        $profile->licenses_message = $message;
        $profile->save();

        return redirect()->route('dashboard.profiles.view', ['profile' => $profile])->with(['message' => 'وضعیت مدارک با موفقیت ثبت شد.']);
    }
}
