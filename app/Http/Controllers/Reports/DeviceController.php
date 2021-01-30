<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Variables\Device;
use App\Models\Variables\DeviceType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $devicePhysicalStatus1 = Device::where('physical_status', 1)->count();
        $devicePhysicalStatus2 = Device::where('physical_status', 2)->count();
        $deviceTransportStatus1 = Device::where('transport_status', 1)->count();
        $deviceTransportStatus2 = Device::where('transport_status', 2)->count();
        $deviceTransportStatus3 = Device::where('transport_status', 3)->count();
        $devicePspStatus1 = Device::where('psp_status', 1)->count();
        $devicePspStatus2 = Device::where('psp_status', 2)->count();
        $devicesSummary = [
            'physicalStatus' => [$devicePhysicalStatus1, $devicePhysicalStatus2],
            'transportStatus' => [$deviceTransportStatus1, $deviceTransportStatus2,$deviceTransportStatus3],
            'pspStatus' => [$devicePspStatus1, $devicePspStatus2],
        ];

        $deviceModelsChartData = $this->deviceChartData();
        return Inertia::render('Dashboard/Reports/Devices', compact('devicesSummary', 'deviceModelsChartData'));
    }

    private function deviceChartData()
    {
        $list = [];
        $deviceTypesChartLabels = [];
        $deviceTypesChart = [];
        $models = DeviceType::withCount('devices')->orderBy('name', 'ASC')->get();
        foreach ($models as $model) {
            $total = Device::where('device_type_id', $model->id)->count();
            $devicePhysicalStatus1 = Device::where('device_type_id', $model->id)->where('physical_status', 1)->count();
            $devicePhysicalStatus2 = Device::where('device_type_id', $model->id)->where('physical_status', 2)->count();
            $deviceTransportStatus1 = Device::where('device_type_id', $model->id)->where('transport_status', 1)->count();
            $deviceTransportStatus2 = Device::where('device_type_id', $model->id)->where('transport_status', 2)->count();
            $deviceTransportStatus3 = Device::where('device_type_id', $model->id)->where('transport_status', 3)->count();
            $devicePspStatus1 = Device::where('device_type_id', $model->id)->where('psp_status', 1)->count();
            $devicePspStatus2 = Device::where('device_type_id', $model->id)->where('psp_status', 2)->count();
            $list['table'][] = [
                'name' => $model->name,
                'total' => $total,
                'devicePhysicalStatus1' => $devicePhysicalStatus1,
                'devicePhysicalStatus2' => $devicePhysicalStatus2,
                'deviceTransportStatus1' => $deviceTransportStatus1,
                'deviceTransportStatus2' => $deviceTransportStatus2,
                'deviceTransportStatus3' => $deviceTransportStatus3,
                'devicePspStatus1' => $devicePspStatus1,
                'devicePspStatus2' => $devicePspStatus2,
            ];
        }

        return $list;
    }
}
