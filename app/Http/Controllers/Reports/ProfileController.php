<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Profiles\Profile;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Morilog\Jalali\Jalalian;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $years = [1399, 1400];
        $now = Jalalian::now();
        $year = (int)$request->query('year', $now->getYear());
        $months = monthOfYear($year, $now->isLeapYear());
        $month = (int)$request->query('month', $now->getMonth());
        $agentId = (int)$request->query('agent', 0);
        $marketer = (int)$request->query('marketer', 0);
        $agent = User::where('id', $agentId)->get()->first();
        $monthLabels = [];
        foreach ($months as $m) {
            $monthLabels[] = $m[0];
        }

        $yearChartData = $this->getYearChartData($months, $agentId);
        $monthChartData = $this->getMonthChartData($months[($month - 1)], $agentId);

        $totalProfilesCount = Profile::where('status', '!=', 0)->count();

        $startDayOfYear = Jalalian::fromFormat('Y/m/d', $year . '/01/01')->toCarbon();
        $endDayOfYear = Jalalian::fromFormat('Y/m/d', $year . '/12/' . ($now->isLeapYear() ? '30' : '29'))->toCarbon();
        $thisYearProfilesCount = Profile::whereBetween('created_at', [$startDayOfYear, $endDayOfYear])->where('status', '!=', 0)->count();

        $startDayOfMonth = Jalalian::fromFormat('Y/m/d', $year . '/' . $month . '/01')->toCarbon();
        $endDayOfMonth = Jalalian::fromFormat('Y/m/d', $year . '/' . $month . '/' . $now->getMonthDays())->toCarbon();
        $thisMonthProfilesCount = Profile::whereBetween('created_at', [$startDayOfMonth, $endDayOfMonth])->where('status', '!=', 0)->count();

        $agentsChartData = $this->getAgentsChartData();
        $thisAgentProfilesCount = 0;
        $marketersChartData = [];
        if ($agentId != 0) {
            $marketersChartData = $this->getMarketersChartData($agentId);
            $thisAgentProfilesCount = Profile::where('user_id', $agentId)->where('status', '!=', 0)->count();
        }
        return Inertia::render('Dashboard/Reports/Profiles', [
            'years' => $years,
            'thisYear' => $year,
            'thisMonth' => $month,
            'monthLabels' => $monthLabels,
            'totalProfilesCount' => $totalProfilesCount,
            'thisYearProfilesCount' => $thisYearProfilesCount,
            'thisMonthProfilesCount' => $thisMonthProfilesCount,

            'thisAgentProfilesCount' => $thisAgentProfilesCount,

            'yearChartData' => $yearChartData,
            'monthChartData' => $monthChartData,

            'agentsChartData' => $agentsChartData,
            'thisAgent' => $agent,

            'thisMarketer' => $marketer,
            'marketersChartData' => $marketersChartData
        ]);
    }

    /**
     * @param array $months لیست ماههای سال
     * @param int $agentId شناسه نماینده یا بازاریاب
     * @return array
     */
    private function getYearChartData(array $months, int $agentId): array
    {
        $profilesChartLabels = [];
        $profileChartBackgrounds = [];
        $profileChartData = [];
        foreach ($months as $month) {
            $start = Jalalian::fromFormat('Y/m/d', $month[1])->toCarbon();
            $end = Jalalian::fromFormat('Y/m/d', $month[2])->toCarbon();
            $profilesCount = Profile::whereBetween('created_at', [$start, $end])->where(function ($query) use ($agentId) {
                if ($agentId !== 0) {
                    $query->where('user_id', $agentId);
                    $query->orWhere(function ($query) use ($agentId) {
                        $query->whereHas('user', function ($query) use ($agentId) {
                            $query->where('parent_id', $agentId);
                        });
                    });
                }
            })->where('status', '!=', 0)->count();
            $profilesChartLabels[] = $month[0];
            $profileChartData[] = $profilesCount;
            $profileChartBackgrounds[] = generateRandomColor();
        }

        $profilesChartDatasets = [
            'label' => 'پرونده های ثبت  شده',
            'backgroundColor' => $profileChartBackgrounds,
            'data' => $profileChartData
        ];

        return [
            'labels' => $profilesChartLabels,
            'datasets' => [$profilesChartDatasets],
        ];
    }

    public function getMonthChartData($month, int $agentId): array
    {
        $startDayOfMonth = Jalalian::fromFormat('Y/m/d', $month[1])->toCarbon();
        $endDayOfMonth = Jalalian::fromFormat('Y/m/d', $month[2])->toCarbon();

        $period = CarbonPeriod::create($startDayOfMonth, $endDayOfMonth);

        $profilesCount = [];
        $profilesBackground = [];
        $profilesChartLabels = [];
        foreach ($period as $date) {
            $s = $date->hour(0)->minute(0)->second(0)->format('Y-m-d H:i:s');
            $e = $date->hour(23)->minute(59)->second(59)->format('Y-m-d H:i:s');
            $profilesCount[] = Profile::whereBetween('created_at', [$s, $e])->where(function ($query) use ($agentId) {
                if ($agentId !== 0) {
                    $query->where('user_id', $agentId);
                    $query->orWhere(function ($query) use ($agentId) {
                        $query->whereHas('user', function ($query) use ($agentId) {
                            $query->where('parent_id', $agentId);
                        });
                    });
                }
            })->where('status', '!=', 0)->count();
            $profilesBackground[] = generateRandomColor();
            $profilesChartLabels[] = Jalalian::forge($date->format('Y/m/d'))->format('Y/m/d');
        }

        $profilesChartDatasets = [
            'label' => 'پرونده های ثبت شده در ماه ' . $month[0],
            'backgroundColor' => $profilesBackground,
            'data' => $profilesCount
        ];

        return [
            'labels' => $profilesChartLabels,
            'datasets' => [$profilesChartDatasets],
        ];
    }

    public function getAgentsChartData(): array
    {
        $agents = User::where('level', 'AGENT')->orderBy('name', 'ASC')->get();
        $profilesCount = [];
        $profilesBackground = [];
        $agentsChartLabels = [];
        foreach ($agents as $agent) {
            $profilesCount[] = Profile::where(function ($query) use ($agent) {
                $query->where('user_id', $agent->id);
                $query->orWhere(function ($query) use ($agent) {
                    $query->whereHas('user', function ($query) use ($agent) {
                        $query->where('parent_id', $agent->id);
                    });
                });
            })->where('status', '!=', 0)->count();
            $profilesBackground[] = generateRandomColor();
            $agentsChartLabels[] = $agent->name;
        }

        $profilesChartDatasets = [
            'label' => 'پرونده های ثبت شده توسط نمایندگان',
            'backgroundColor' => $profilesBackground,
            'data' => $profilesCount
        ];

        return [
            'labels' => $agentsChartLabels,
            'datasets' => [$profilesChartDatasets],
            'agents' => $agents
        ];
    }

    /**
     * @param int $agentId
     * @return array
     */
    private function getMarketersChartData(int $agentId): array
    {
        $marketers = User::where('level', 'MARKETER')->where('parent_id', $agentId)->orderBy('name', 'ASC')->get();
        $profilesCount = [];
        $profilesBackground = [];
        $marketersChartLabels = [];
        foreach ($marketers as $marketer) {
            $profilesCount[] = Profile::where('user_id', $marketer->id)->count();
            $profilesBackground[] = generateRandomColor();
            $marketersChartLabels[] = $marketer->name;
        }

        $profilesChartDatasets = [
            'label' => 'پرونده های ثبت شده توسط بازاریابان',
            'backgroundColor' => $profilesBackground,
            'data' => $profilesCount
        ];

        return [
            'labels' => $marketersChartLabels,
            'datasets' => [$profilesChartDatasets],
            'marketers' => $marketers
        ];
    }
}
