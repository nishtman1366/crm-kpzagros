<?php

namespace App\Imports\Devices;

use App\Models\User;
use App\Models\Variables\Device;
use App\Models\Variables\DeviceConnectionType;
use App\Models\Variables\DeviceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class DeviceImport implements ToModel, WithStartRow
{
    private $user;

    /**
     * DeviceImport constructor.
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }


    public function model(array $row)
    {
        $connectionType = DeviceConnectionType::firstOrCreate([
            'name' => strtoupper(str_replace(' ', '', $row[0]))
        ]);
        $deviceType = DeviceType::firstOrCreate([
            'name' => $row[1],
            'device_connection_type_id' => $connectionType->id
        ]);
        $physicalStatus = $row[7] === 'سالم' ? 1 : 2;
        $transportStatus = 1;
        switch ($row[8]) {
            case 'موجود در انبار':
                $transportStatus = 1;
                break;
            case 'در انتظار نصب':
                $transportStatus = 2;
                break;
            case 'نصب شده':
                $transportStatus = 3;
                break;
        }
        $pspStatus = $row[9] === 'در انتظار تخصیص' ? 1 : 2;

        $guarantee_start = is_null($row[5]) ? null : str_replace('/', '-', substr($row[5], 0, 10));
        $guarantee_end = is_null($row[6]) ? null : str_replace('/', '-', substr($row[6], 0, 10));

        $user = $this->user;
        $owner = User::where('name', $row[3])->get()->first();
        if (!is_null($owner)) {
            $user = $owner;
        }
        return Device::create(
            [
                'serial' => $row[2],
                'device_type_id' => $deviceType->id,
                'user_id' => $user->id,
                'physical_status' => $physicalStatus,
                'transport_status' => $transportStatus,
                'psp_status' => $pspStatus,
                'guarantee_start' => $guarantee_start,
                'guarantee_end' => $guarantee_end,
                'status' => 2,
            ]
        );
    }
    
    public function startRow(): int
    {
        return 2;
    }
}
