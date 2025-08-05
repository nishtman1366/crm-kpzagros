<?php

namespace App\Exports\Devices;

use App\Models\Variables\Device;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;


class DevicesWithImei implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    private Collection $collection;

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
        return $this->collection->map(function (Device $item) {
            return [
                'psp' => $item->terminal?->profile?->psp?->name ?? '-',
                'type' => $item->deviceType?->type?->name ?? '-',
                'model' => $item->deviceType?->name ?? '-',
                'serial' => toEnglishNumbers(trim($item->serial)),
                'imei' => toEnglishNumbers(trim($item->imei)),
                'sim_number' => toEnglishNumbers(trim($item->sim_number)),
                'national_code' => $item->terminal?->profile?->customer?->national_code ?? '-',
                'name' => $item->terminal?->profile?->customer?->full_name ?? '-',
                'mobile' => $item->terminal?->profile?->customer?->mobile ?? '-',
                'business_name' => $item->terminal?->profile?->business?->name ?? '-',
                'terminal_number' => $item->terminal?->terminal_number ?? '-',
                'merchant_number' => $item->terminal?->profile?->merchant_id ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'سرویس‌دهنده',
            'نوع ارتباط',
            'مدل دستگاه',
            'سریال دستگاه',
            'imei',
            'شماره سیم‌کارت',
            'کد ملی پذیرنده',
            'نام پذیرنده',
            'شماره همراه',
            'نام کسب و کار',
            'شماره ترمینال',
            'شماره پذیرنده',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // اعمال به کل محدوده داده‌ها
            'A1:L' . ($this->collection->count() + 1) => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            '1:1' => [
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['rgb' => '5BC3BE']
                ],
            ],

        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // تبدیل 0.35 اینچ به پوینت (1 اینچ = 72 پوینت)
                $rowHeight = 0.35 * 72;

                // اعمال ارتفاع به تمام سطرها
                foreach ($event->sheet->getDelegate()->getRowIterator() as $row) {
                    $event->sheet->getDelegate()->getRowDimension($row->getRowIndex())->setRowHeight($rowHeight);
                }
            },
        ];
    }

}
