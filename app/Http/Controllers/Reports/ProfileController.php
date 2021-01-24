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
        $agent = (int)$request->query('agent', 0);

        $monthLabels = [];
        foreach ($months as $m) {
            $monthLabels[] = $m[0];
        }

        $yearChartData = $this->getYearChartData($months);
        $monthChartData = $this->getMonthChartData($months[($month - 1)]);

        $startDayOfYear = Jalalian::fromFormat('Y/m/d', $year . '/01/01')->toCarbon();
        $endDayOfYear = Jalalian::fromFormat('Y/m/d', $year . '/12/' . ($now->isLeapYear() ? '30' : '29'))->toCarbon();
        $totalProfilesCount = Profile::where('status', '!=', 0)->count();
        $thisYearProfilesCount = Profile::whereBetween('created_at', [$startDayOfYear, $endDayOfYear])->where('status', '!=', 0)->count();

        $agentsChartData = $this->getAgentsChartData();

        return Inertia::render('Dashboard/Reports/Profiles', [
            'years' => $years,
            'thisYear' => $year,
            'thisMonth' => $month,
            'monthLabels' => $monthLabels,
            'totalProfilesCount' => $totalProfilesCount,
            'thisYearProfilesCount' => $thisYearProfilesCount,

            'yearChartData' => $yearChartData,
            'monthChartData' => $monthChartData,

            'agentsChartData' => $agentsChartData,
            'thisAgent' => $agent
        ]);
    }

    public function getYearChartData($months)
    {
        $profilesChartLabels = [];
        $profileChartBackgrounds = [];
        $profileChartData = [];
        foreach ($months as $month) {
            $start = Jalalian::fromFormat('Y/m/d', $month[1])->toCarbon();
            $end = Jalalian::fromFormat('Y/m/d', $month[2])->toCarbon();
            $profilesCount = Profile::whereBetween('created_at', [$start, $end])->count();
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

    public function getMonthChartData($month)
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
            $profilesCount[] = Profile::whereBetween('created_at', [$s, $e])->where('status', '!=', 0)->count();
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

    public function getAgentsChartData()
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
            })->count();
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
}
