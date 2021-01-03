<?php

namespace App\Http\Controllers;

use App\Models\Profiles\Profile;
use App\Models\Variables\Device;
use App\Models\Variables\DeviceType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Morilog\Jalali\Jalalian;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $profileStatuses = ['ثبت موقت', 'ثبت شده', 'در انتظار بررسی مدارک', 'تایید مدارک', 'ثبت در PSP', 'تایید شاپرک', 'در انتظار تخصیص',
            'تخصیص داده شده', 'نصب شده', 'ابطال', 'عدم تایید مدارک', 'عدم تایید شاپرک'];
        $profileCounts = [];
        $user = Auth::user();
        $date = $request->query('date', null);
//        DB::enableQueryLog();
        foreach ($profileStatuses as $key => $status) {
            $profileCounts[] = Profile::where('status', $key)->where(function ($query) use ($date, $key) {
                $field = 'updated_at';
                if ($key == 0) {
                    $field = 'created_at';
                }
                if (!is_null($date)) {
                    $query->whereBetween($field, $this->getDateValues($date));
                }
            })
                ->where(function ($profilesQuery) use ($user) {
                    $profilesQuery->where('user_id', $user->id);

                    if ($user->isAgent()) {
                        $profilesQuery->orWhereHas('user', function ($query) use ($user) {
                            $query->where('parent_id', $user->id);
                        });
                    }

                    if ($user->isAdmin()) {
                        $profilesQuery->orWhereHas('user.parent', function ($query) use ($user) {
                            $query->where('parent_id', $user->id);
                        });
                    }
                })
                ->count();
        }
//        dd(DB::getQueryLog());

        $deviceTypes = DeviceType::orderBy('name', 'ASC')->get();
        $deviceTypesChart = [];
        $deviceTypesChartLabels = [];
        foreach ($deviceTypes as $type) {
            $deviceTypesChartLabels[] = $type->name;
        }
        $physicalStatus1Count = [];
        foreach ($deviceTypes as $type) {
            $physicalStatus1Count[] = Device::where(function($devicesQuery)use($user){
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
            })->where('device_type_id', $type->id)->where('physical_status', 1)->count();
        }
        $deviceTypesChart[] = [
            'label' => 'سالم',
            'backgroundColor' => generateRandomColor(),
            'data' => $physicalStatus1Count
        ];
        $physicalStatus2Count = [];
        foreach ($deviceTypes as $type) {
            $physicalStatus2Count[] = Device::where(function($devicesQuery)use($user){
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
            })->where('device_type_id', $type->id)->where('physical_status', 2)->count();
        }
        $deviceTypesChart[] = [
            'label' => 'خراب',
            'backgroundColor' => generateRandomColor(),
            'data' => $physicalStatus2Count
        ];
        $transportStatus1Count = [];
        foreach ($deviceTypes as $type) {
            $transportStatus1Count[] = Device::where(function($devicesQuery)use($user){
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
            })->where('device_type_id', $type->id)->where('transport_status', 1)->count();
        }
        $deviceTypesChart[] = [
            'label' => 'موجود در انبار',
            'backgroundColor' => generateRandomColor(),
            'data' => $transportStatus1Count
        ];
        $transportStatus2Count = [];
        foreach ($deviceTypes as $type) {
            $transportStatus2Count[] = Device::where(function($devicesQuery)use($user){
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
            })->where('device_type_id', $type->id)->where('transport_status', 2)->count();
        }
        $deviceTypesChart[] = [
            'label' => 'در انتظار نصب',
            'backgroundColor' => generateRandomColor(),
            'data' => $transportStatus2Count
        ];
        $transportStatus3Count = [];
        foreach ($deviceTypes as $type) {
            $transportStatus3Count[] = Device::where(function($devicesQuery)use($user){
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
            })->where('device_type_id', $type->id)->where('transport_status', 3)->count();
        }
        $deviceTypesChart[] = [
            'label' => 'نصب شده',
            'backgroundColor' => generateRandomColor(),
            'data' => $transportStatus3Count
        ];
        return Inertia::render('Dashboard/Reports/ReportsList', [
            'profileLabels' => $profileStatuses,
            'profileCounts' => $profileCounts,

            'deviceTypesChart' => $deviceTypesChart,
            'deviceTypesChartLabels' => $deviceTypesChartLabels,
        ]);
    }

    public function getDateValues($date)
    {
        switch ($date) {
            case 'today':
                return [
                    Carbon::today()->hour(0)->minute(0)->second(0),
                    Carbon::today()->hour(23)->minute(59)->second(59)
                ];
                break;
            case 'yesterday':
                return [
                    Carbon::yesterday()->hour(0)->minute(0)->second(0),
                    Carbon::yesterday()->hour(23)->minute(59)->second(59)
                ];
                break;
            case 'week':
                $gDateStart = Jalalian::forge('last saturday')->toCarbon();
                $gDateEnd = Jalalian::now()->toCarbon();
                return [
                    $gDateStart->hour(0)->minute(0)->second(0),
                    $gDateEnd->hour(23)->minute(59)->second(59)
                ];
                break;
            case 'lastWeek':
                $gDateStart = Jalalian::forge('last saturday')->subDays(7)->toCarbon();
                $gDateEnd = Jalalian::forge('last saturday')->toCarbon();
                return [
                    $gDateStart->hour(0)->minute(0)->second(0),
                    $gDateEnd->hour(23)->minute(59)->second(59)
                ];
                break;
            case 'month':
                $month = Jalalian::now()->getMonth();
                $year = Jalalian::now()->getYear();
                $jDate = new Jalalian($year, $month, 1);
                $gDateStart = $jDate->toCarbon();
                $gDateEnd = Jalalian::now()->toCarbon();
                return [
                    $gDateStart->hour(0)->minute(0)->second(0),
                    $gDateEnd->hour(23)->minute(59)->second(59)
                ];
                break;
            case 'lastMonth':
                $month = Jalalian::now()->subMonths(1)->getMonth();
                $year = Jalalian::now()->subMonths(1)->getYear();
                $jDate = new Jalalian($year, $month, 1);
                $gDateStart = $jDate->toCarbon();
                $gDateEnd = $jDate->addMonths(1)->toCarbon();
                return [
                    $gDateStart->hour(0)->minute(0)->second(0),
                    $gDateEnd->hour(23)->minute(59)->second(59)
                ];
                break;
            case '3month':
                $month = Jalalian::now()->subMonths(2)->getMonth();
                $year = Jalalian::now()->subMonths(2)->getYear();
                $jDate = new Jalalian($year, $month, 1);
                $gDateStart = $jDate->toCarbon();
                $gDateEnd = Jalalian::now()->toCarbon();
                return [
                    $gDateStart->hour(0)->minute(0)->second(0),
                    $gDateEnd->hour(23)->minute(59)->second(59)
                ];
                break;
            case 'last3month':
                $month = Jalalian::now()->subMonths(5)->getMonth();
                $year = Jalalian::now()->subMonths(5)->getYear();
                $jDate = new Jalalian($year, $month, 1);
                $gDateStart = $jDate->toCarbon();
                $gDateEnd = $jDate->addMonths(3)->toCarbon();
                return [
                    $gDateStart->hour(0)->minute(0)->second(0),
                    $gDateEnd->hour(23)->minute(59)->second(59)
                ];
                break;
            case '6month':
                $month = Jalalian::now()->subMonths(5)->getMonth();
                $year = Jalalian::now()->subMonths(5)->getYear();
                $jDate = new Jalalian($year, $month, 1);
                $gDateStart = $jDate->toCarbon();
                $gDateEnd = Jalalian::now()->toCarbon();
                return [
                    $gDateStart->hour(0)->minute(0)->second(0),
                    $gDateEnd->hour(23)->minute(59)->second(59)
                ];
                break;
            case 'last6month':
                $month = Jalalian::now()->subMonths(11)->getMonth();
                $year = Jalalian::now()->subMonths(11)->getYear();
                $jDate = new Jalalian($year, $month, 1);
                $gDateStart = $jDate->toCarbon();
                $gDateEnd = $jDate->addMonths(6)->toCarbon();
                return [
                    $gDateStart->hour(0)->minute(0)->second(0),
                    $gDateEnd->hour(23)->minute(59)->second(59)
                ];
                break;
            case 'year':
                $year = Jalalian::now()->getYear();
                $jDate = new Jalalian($year, 1, 1);
                $gDateStart = $jDate->toCarbon();
                $gDateEnd = Jalalian::now()->toCarbon();
                return [
                    $gDateStart->hour(0)->minute(0)->second(0),
                    $gDateEnd->hour(23)->minute(59)->second(59)
                ];
                break;
            case 'lastYear':
                $year = Jalalian::now()->subYears(1)->getYear();
                $jDate = new Jalalian($year, 1, 1);
                $gDateStart = $jDate->toCarbon();
                $gDateEnd = $jDate->addYears(1)->toCarbon();
                return [
                    $gDateStart->hour(0)->minute(0)->second(0),
                    $gDateEnd->hour(23)->minute(59)->second(59)
                ];
                break;
        }
    }
}
