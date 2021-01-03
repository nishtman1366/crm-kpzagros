<?php

namespace App\Exports\Repairs;

use App\Models\Repairs\Event;
use App\Models\Repairs\RepairTypesList;
use App\Models\Repairs\Type;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RepairExport implements FromCollection, WithHeadings
{
    private $collection;

    /**
     * DeviceExport constructor.
     * @param $collection
     */
    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $list = collect([]);
        foreach ($this->collection as $item) {
            $itemRepairTypes = RepairTypesList::where('repair_id', $item->id)->pluck('type_id');
            $repairTypes = Type::whereIn('id', $itemRepairTypes)->pluck('name');
            $repairEndEvent = Event::where('repair_id', $item->id)->where('status', 4)->get()->first();
            $returnEvent = Event::where('repair_id', $item->id)->where('status', 7)->get()->first();

            $list->push([
                'date' => $item->jCreatedAt,
                'tracking_code' => $item->tracking_code,
                'device_type_name' => is_null($item->deviceType) ? '' : $item->deviceType->name,
                'serial' => $item->serial,
                'types' => implode(', ', $repairTypes->toArray()),
                'psp_name' => is_null($item->psp) ? '' : $item->psp->name,
                'guarantee_name' => $item->guarantee_end,
                'new_serial' => $item->new_serial,
                'loan_serial' => $item->loan_serial,
                'national_code' => $item->national_code,
                'location_name' => is_null($item->Location) ? '' : $item->location->name,
                'repair_end_date' => is_null($repairEndEvent) ? '' : $repairEndEvent->jDate,
                'price' => $item->price,
                'deposit' => $item->deposit,
                'name' => $item->name,
                'mobile' => $item->mobile,
                'technical_description' => $item->technical_description,
                'description' => $item->description,
                'return_date' => is_null($returnEvent) ? '' : $returnEvent->jDate,
            ]);
        }
        return $list;
    }

    public function headings(): array
    {
        return [
            'تاریخ پذیرش',
            'کد پیگیری',
            'مدل دستگاه',
            'سریال دستگاه',
            'علت خرابی',
            'سرویس دهنده',
            'تاریخ انقضاء گارانتی',
            'سریال دستگاه جایگزین',
            'سریال دستگاه امانت',
            'کدملی پذیرنده',
            'محل  تعمیر',
            'تاریخ اتمام تعمیرات',
            'هزینه تعمیرات',
            'ودیعه دستگاه امانی',
            'نام پذیرنده',
            'تلفن همراه پذیرنده',
            'توضیحات واحد تعمیرات',
            'توضیحات تکمیلی',
            'تاریخ تحویل به پذیرنده'
        ];
    }
}
