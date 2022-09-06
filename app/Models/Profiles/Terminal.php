<?php

namespace App\Models\Profiles;

use App\Models\Variables\Device;
use App\Models\Variables\DeviceConnectionType;
use App\Models\Variables\DeviceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terminal extends Model
{
    use HasFactory;

    protected $fillable = ['profile_id', 'type', 'device_type_id', 'device_connection_type_id', 'terminal_number', 'device_id', 'status',
        'reject_reason', 'cancel_reason', 'change_reason', 'new_device_type_id', 'new_device_id', 'setup_date',
        'access_address', 'access_port', 'callback_address', 'callback_port', 'description',
        'device_sell_type', 'device_amount', 'device_dept_profile_id', 'device_physical_status'];

    protected $appends = ['statusText', 'typeText', 'devicePhysicalStatusText', 'deviceSellTypeText'];

    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            default:
            case 0:
                return 'ثبت شده';
            case 1:
                return 'در انتظار تخصیص';
            case 2:
                return 'عدم تایید سریال';
            case 3:
                return 'تخصیص داده‌شده';
            case 4:
                return 'درخواست ابطال';
            case 5:
                return 'ابطال شده';
            case 6:
                return 'نصب شده';
            case 7:
                return 'درخواست جابجایی';
            case 8:
                return 'انتخاب سریال جدید';
            case 9:
                return 'رد درخواست جابجایی';
            case 10:
                return 'رد درخواست ابطال';
            case 254:
                return 'نامشخص';
        }
    }

    public function getTypeTextAttribute()
    {
        switch ($this->attributes['type']) {
            default:
            case 'POS':
                return 'پایانه فروش رومیزی';
            case 'IPG':
                return 'درگاه پرداخت اینترنتی';
            case 'WPOS':
                return 'پایانه فروش بیسیم';
        }
    }

    public function getDevicePhysicalStatusTextAttribute()
    {
        switch ($this->attributes['device_physical_status']) {
            default:
                return 'ثبت نشده';
            case 'new':
                return 'آکبند';
            case 'stock':
                return 'کارکرده';
        }
    }

    public function getDeviceSellTypeTextAttribute()
    {
        switch ($this->attributes['device_sell_type']) {
            default:
                return 'ثبت نشده';
            case 'cash':
                return 'نقدی';
            case 'dept':
                return 'امانی';
            case 'installment':
                return 'اقساطی';
        }
    }

    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class, 'device_type_id', 'id');
    }

    public function deviceConnectionType()
    {
        return $this->belongsTo(DeviceConnectionType::class, 'device_connection_type_id', 'id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

}
