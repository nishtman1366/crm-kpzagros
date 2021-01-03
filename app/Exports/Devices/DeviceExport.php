<?php

namespace App\Exports\Devices;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DeviceExport implements FromCollection, WithHeadings
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
            $list->push([
                'type' => $item->deviceType->type->name,
                'model' => $item->deviceType->name,
                'serial' => $item->serial,
                'owner' => $item->user ? $item->user->name : '',
                'createdDate' => $item->jCreatedAt,
                'guaranteeStart' => str_replace('-', '/', $item->guarantee_start),
                'guaranteeEnd' => str_replace('-', '/', $item->guarantee_end),
                'physicalStatusText' => $item->physicalStatusText,
                'transportStatusText' => $item->transportStatusText,
                'pspStatusText' => $item->pspStatusText,
                'lastUpdate' => $item->jUpdatedAt,
            ]);
        }
        return $list;
    }

    public function headings(): array
    {
        return [
            'نوع ارتباط',
            'مدل دستگاه',
            'سریال دستگاه',
            'مالک',
            'زمان ثبت',
            'تاریخ شروع گارانتی',
            'تاریخ پایان گارانتی',
            'وضعیت فیزیکی',
            'وضعیت انبار',
            'وضعیت PSP',
            'آخرین تغییرات',
        ];
    }
}
