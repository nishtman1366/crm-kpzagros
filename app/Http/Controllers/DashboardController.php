<?php

namespace App\Http\Controllers;

use App\Models\Profiles\Profile;
use App\Models\Profiles\ProfileMessage;
use App\Models\User;
use App\Models\Variables\Device;
use App\Models\Variables\DeviceType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Morilog\Jalali\Jalalian;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $profileStatus = [
            'status1' => $this->getProfileStatusForDashboard($user, 1, 1),
            'status2' => $this->getProfileStatusForDashboard($user, 2),
            'status7' => $this->getProfileStatusForDashboard($user, 7),
            'status11' => $this->getProfileStatusForDashboard($user, 11),
        ];

        $events = $this->getEventsForDashboard($user);

        $topMarketersChart = $this->getTopMarketersChartForDashboard($user);

        $devicesStatus = $this->getDevicesStatusForDashboard($user);

        $devicesChartData = $this->getDevicesChartData($user);
        
        return Inertia::render('Dashboard', [
            'profileStatus' => $profileStatus,
            'events' => $events,
            'topMarketersChartDatasets' => $topMarketersChart['data'],
            'topMarketersChartLabels' => $topMarketersChart['labels'],
            'devicesStatus' => $devicesStatus,
            'devicesChartDatasets' => $devicesChartData['data'],
            'devicesChartLabels' => $devicesChartData['labels'],
        ]);
    }

    private function getProfileStatusForDashboard($user, $status, $today = 0)
    {
        $profilesQuery = Profile::with('user')
            ->with('user.parent')
            ->whereHas('customer')
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
            });


        $profilesQuery->where('status', $status);

        if ($today === 1) {
            $profilesQuery->whereBetween('created_at', [
                Carbon::today()->hour(0)->minute(0)->second(0),
                Carbon::today()->hour(23)->minute(59)->second(59)
            ]);
        }
        return $profilesQuery->count();
    }

    private function getEventsForDashboard($user)
    {
        $eventsQuery = ProfileMessage::with('profile')
            ->where(function ($query) use ($user) {
                $query->whereHas('profile', function ($profileQuery) use ($user) {
                    $profileQuery->where('user_id', $user->id);
                    if ($user->isAgent()) {
                        $profileQuery->orWhereHas('user.parent', function ($userParentQuery) use ($user) {
                            $userParentQuery->where('id', $user->id);
                        });
                    }
                    if ($user->isAdmin()) {
                        $profileQuery->orWhereHas('user.parent.parent', function ($userParentParentQuery) use ($user) {
                            $userParentParentQuery->where('id', $user->id);
                        });
                    }
                })
                    ->orWhere('user_id', $user->id);
            })
            ->with('profile.customer');

        return $eventsQuery->orderBy('id', 'DESC')->limit(30)
            ->get();
    }

    private function getTopMarketersChartForDashboard($user)
    {
        $topMarketersChartData = [];
        $topMarketersChartLabels = [];
        $topMarketersChartBackgrounds = [];
        $topMarketers = DB::table('profiles')
            ->select(DB::raw('user_id'), DB::raw('count(*) as count'))
            ->groupBy('user_id')
            ->orderBy('count', 'desc')
            ->where('status', 8)
            ->where(function ($query) use ($user) {
                if ($user->isAgent()) {
                    $marketers = User::where('parent_id', $user->id)->pluck('id');
                    $query->whereIn('user_id', $marketers);
                }
                if ($user->isAdmin()) {
                    $agents = User::where('parent_id', $user->id)->where('level', 'AGENT')->pluck('id');
                    $marketers = User::where('parent_id', $user->id)->where('level', 'MARKETER')->pluck('id');
                    $agentMarketers = User::whereIn('parent_id', $agents)->pluck('id');
                    $query->whereIn('user_id', $agents);
                    $query->orWhereIn('user_id', $marketers);
                    $query->orWhereIn('user_id', $agentMarketers);
                }
            })
            ->take(10)
            ->get();
        foreach ($topMarketers as $item) {
            $user = User::where('id', $item->user_id)->get()->first();
            $topMarketersChartData[] = $item->count;
            $topMarketersChartLabels[] = $user->name;
            $topMarketersChartBackgrounds[] = generateRandomColor();
        }
        return [
            'data' => [
                'label' => 'مجموع فروش',
                'backgroundColor' => $topMarketersChartBackgrounds,
                'data' => $topMarketersChartData
            ],
            'labels' => $topMarketersChartLabels
        ];
    }

    private function getDevicesStatusForDashboard($user)
    {
        $devicesStatus = [
            'physicalStatus1' => Device::where('physical_status', 1)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                    if ($user->isAdmin()) {
                        $query->orWhereHas('user', function ($userQuery) use ($user) {
                            $userQuery->where('parent_id', $user->id);
                        });
                    }
                })->count(),
            'physicalStatus2' => Device::where('physical_status', 2)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                    if ($user->isAdmin()) {
                        $query->orWhereHas('user', function ($userQuery) use ($user) {
                            $userQuery->where('parent_id', $user->id);
                        });
                    }
                })->count(),
            'transportStatus1' => Device::where('transport_status', 1)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                    if ($user->isAdmin()) {
                        $query->orWhereHas('user', function ($userQuery) use ($user) {
                            $userQuery->where('parent_id', $user->id);
                        });
                    }
                })->count(),
            'transportStatus2' => Device::where('transport_status', 2)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                    if ($user->isAdmin()) {
                        $query->orWhereHas('user', function ($userQuery) use ($user) {
                            $userQuery->where('parent_id', $user->id);
                        });
                    }
                })->count(),
            'transportStatus3' => Device::where('transport_status', 3)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                    if ($user->isAdmin()) {
                        $query->orWhereHas('user', function ($userQuery) use ($user) {
                            $userQuery->where('parent_id', $user->id);
                        });
                    }
                })->count(),
            'pspStatus1' => Device::where('psp_status', 1)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                    if ($user->isAdmin()) {
                        $query->orWhereHas('user', function ($userQuery) use ($user) {
                            $userQuery->where('parent_id', $user->id);
                        });
                    }
                })->count(),
            'pspStatus2' => Device::where('psp_status', 2)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                    if ($user->isAdmin()) {
                        $query->orWhereHas('user', function ($userQuery) use ($user) {
                            $userQuery->where('parent_id', $user->id);
                        });
                    }
                })->count(),
        ];

        return $devicesStatus;
    }

    private function getDevicesChartData($user)
    {
        $devicesChartData = [];
        $devicesChartLabels = [];
        $devicesChartBackgrounds = [];
        $devices = DeviceType::orderBy('name', 'ASC')->get();
        foreach ($devices as $device) {
            $deviceCount = Device::where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                if ($user->isAdmin()) {
                    $query->orWhereHas('user', function ($userQuery) use ($user) {
                        $userQuery->where('parent_id', $user->id);
                    });
                }
            })->where('device_type_id', $device->id)->where('transport_status', 3)->count();
            if ($deviceCount > 0) {
                $devicesChartData[] = $deviceCount;
                $devicesChartLabels[] = $device->name;
                $devicesChartBackgrounds[] = generateRandomColor();
            }
        }

        return [
            'data' => [
                'label' => 'دستگاه های نصب شده',
                'backgroundColor' => $devicesChartBackgrounds,
                'data' => $devicesChartData
            ],
            'labels' => $devicesChartLabels
        ];
    }
}
