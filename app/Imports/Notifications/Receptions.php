<?php

namespace App\Imports\Notifications;

use App\Models\Notifications\Reception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;

class Receptions implements ToModel
{
    private int $batchNotificationId;

    /**
     * Receptions constructor.
     * @param int $batchNotificationId
     */
    public function __construct(int $batchNotificationId)
    {
        $this->batchNotificationId = $batchNotificationId;
    }

    public function model(array $row)
    {
        $row[0] = (string)$row[0];

        if (trim($row[0]) != null && $row[0] != '') {
            $number = $row[0];
            if ($number[0] != '0') {
                $number = '0' . $number;
            }
            return Reception::firstOrCreate([
                'batch_notification_id' => $this->batchNotificationId,
                'reception' => $this->clearMobileNumber($number)
            ]);
        }
    }

    private function clearMobileNumber($number)
    {
        $number = str_replace(' ', '', $number);
        $number = str_replace('-', '', $number);
        $number = str_replace('_', '', $number);
        $number = str_replace('.', '', $number);

        return $number;
    }
}
