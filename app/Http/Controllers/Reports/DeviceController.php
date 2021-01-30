<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Variables\DeviceType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $devicesSummary = [
            'physicalStatus' => [10, 15],
            'transportStatus' => [20, 11],
            'pspStatus' => [14, 74],
        ];

        $deviceModelsChartData = $this->deviceChartData();
        return Inertia::render('Dashboard/Reports/Devices', compact('devicesSummary'));
    }

    private function deviceChartData()
    {
        $models = DeviceType::withCount('devices')->orderBy('name', 'ASC')->get();
        foreach ($models as $model) {

        }
    }
}
