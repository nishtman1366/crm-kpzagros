<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Repairs\Repair;
use App\Models\Repairs\RepairTypesList;
use App\Models\Repairs\Type;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RepairController extends Controller
{
    public function index(Request $request)
    {
        $statuses = [
            ['id' => 0, 'name' => 'ثبت موقت'],
            ['id' => 1, 'name' => 'ثبت شده'],
            ['id' => 2, 'name' => 'دریافت شده توسط واحد فنی'],
            ['id' => 3, 'name' => 'در صف تعمیر'],
            ['id' => 4, 'name' => 'تعمیر شده'],
            ['id' => 5, 'name' => 'در انتظار پرداخت'],
            ['id' => 6, 'name' => 'پرداخت شده'],
            ['id' => 7, 'name' => 'عودت شده'],
            ['id' => 8, 'name' => 'غیرقابل تعمیر']
        ];
        $repairStatuses = [];
        foreach ($statuses as $status) {
            $count = Repair::where('status', $status['id'])->count();
            $repairStatuses[] = [
                'id' => $status['id'],
                'name' => $status['name'],
                'count' => $count
            ];
        }
        $typeChartData = $this->repairTypesChartData();
        return Inertia::render('Dashboard/Reports/Repairs', compact('repairStatuses', 'typeChartData'));
    }

    public function repairTypesChartData()
    {
        $types = Type::orderBy('id', 'ASC')->get();
        $labels = [];
        $data = [];
        $backgrounds = [];
        foreach ($types as $type) {
            $labels[] = $type->name;
            $backgrounds[] = generateRandomColor();
            $data[] = RepairTypesList::where('type_id', $type->id)->count();
        }
        $repairsChartDataSet = [
            'label' => 'پرونده های ثبت  شده',
            'backgroundColor' => $backgrounds,
            'data' => $data
        ];
        return [
            'labels' => $labels,
            'datasets' => [$repairsChartDataSet],
        ];
    }
}
