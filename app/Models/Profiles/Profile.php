<?php

namespace App\Models\Profiles;

use App\Models\Profiles\Customer;
use App\Models\User;
use App\Models\Variables\Device;
use App\Models\Variables\DeviceType;
use App\Models\Variables\Psp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'psp_id', 'device_type_id', 'device_id',
        'multi_account', 'terminal_id', 'merchant_id', 'cancel_reason', 'change_reason', 'new_device_type_id', 'new_device_id', 'status'];

    protected $appends = ['statusText', 'jCreatedAt', 'jUpdatedAt'];

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            case 1:
                return 'ثبت شده';
                break;
            case 2:
                return 'در انتظار بررسی مدارک';
                break;
            case 3:
                return 'تایید مدارک';
                break;
            case 4:
                return 'ثبت در PSP';
                break;
            case 5:
                return 'تایید شاپرک';
                break;
            case 6:
                return 'در انتظار تخصیص';
                break;
            case 7:
                return 'تخصیص داده شده';
                break;
            case 8:
                return 'نصب شده';
                break;
            case 9:
                return 'ابطال';
                break;
            case 10:
                return 'عدم تایید مدارک';
                break;
            case 11:
                return 'عدم تایید شاپرک';
                break;
            case 12:
                return 'درخواست ابطال';
                break;
            case 13:
                return 'عدم تایید سریال';
                break;
            case 14:
                return 'درخواست جابجایی';
                break;
            case 15:
                return 'اختصاص سریال جدید';
                break;
            case 16:
                return 'رد درخواست جابجایی';
                break;
            default:
                return 'ثبت موقت';
                break;
        }
    }

    public function getJCreatedAtAttribute()
    {
        if (is_null($this->attributes['created_at'])) return '';
        return Jalalian::forge($this->attributes['created_at'])->format('Y/m/d H:i:s');
    }

    public function getJUpdatedAtAttribute()
    {
        if (is_null($this->attributes['updated_at'])) return '';
        return Jalalian::forge($this->attributes['updated_at'])->format('Y/m/d H:i:s');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'profile_id', 'id');
    }

    public function business()
    {
        return $this->hasOne(Business::class, 'profile_id', 'id');
    }

    public function accounts()
    {
        return $this->hasMany(ProfilesAccount::class, 'profile_id', 'id');
    }

    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class, 'device_type_id', 'id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }

    public function psp()
    {
        return $this->belongsTo(Psp::class, 'psp_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany(ProfileMessage::class, 'profile_id', 'id');
    }

    public function licenses()
    {
        return $this->hasMany(License::class, 'profile_id', 'id');
    }
}
