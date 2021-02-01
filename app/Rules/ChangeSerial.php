<?php

namespace App\Rules;

use App\Models\Variables\Device;
use Illuminate\Contracts\Validation\Rule;

class ChangeSerial implements Rule
{
    private $deviceTypeId;
    private $messageType;

    /**
     * Create a new rule instance.
     *
     * @param int $deviceTypeId
     */
    public function __construct(int $deviceTypeId)
    {
        $this->deviceTypeId = $deviceTypeId;
        $this->messageType = 1;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $device = Device::where('serial', $value)->get()->first();
        if (is_null($device)) {
            $this->messageType = 1;
            return false;
        }

        if ($device->device_type_id !== $this->deviceTypeId) {
            $this->messageType = 4;
            return false;
        }

        if ($device->physical_status == 2) {
            $this->messageType = 2;
            return false;
        }
        if ($device->transport_status == 2 || $device->transport_status == 3) {
            $this->messageType = 3;
            return false;
        }
        if ($device->psp_status == 2) {
            $this->messageType = 3;
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        switch ($this->messageType) {
            case 1:
                return 'سریال وارد شده در انبار موجود نیست';
            case 2:
                return 'وضعیت فیزیکی سریال وارد شده «خراب» می باشد.';
            case 3:
                return 'سریال وارد شده قبلا تخصیص یافته است.';
            case 4:
                return 'سریال وارد شده برای دستگاه انتخاب شده نمی باشد.';
        }
    }
}
