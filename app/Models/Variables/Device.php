<?php

namespace App\Models\Variables;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\User;
use Morilog\Jalali\Jalalian;

class Device extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'device_type_id', 'serial',
        'physical_status', 'transport_status', 'psp_status', 'guarantee_start', 'guarantee_end',
        'status'];

    protected $appends = ['physicalStatusText', 'transportStatusText', 'pspStatusText', 'statusText', 'jCreatedAt', 'jUpdatedAt'];

    public function getPhysicalStatusTextAttribute()
    {
        switch ($this->attributes['physical_status']) {
            case 1:
            default:
                return 'سالم';
                break;
            case 2:
                return 'خراب';
                break;
        }
    }

    public function getTransportStatusTextAttribute()
    {
        switch ($this->attributes['transport_status']) {
            case 1:
            default:
                return 'موجود در انبار';
                break;
            case 2:
                return 'در انتظار نصب';
                break;
            case 3:
                return 'نصب شده';
                break;
        }
    }

    public function getPspStatusTextAttribute()
    {
        switch ($this->attributes['psp_status']) {
            case 1:
            default:
                return 'در انتظار تخصیص';
                break;
            case 2:
                return 'تخصیص داده شده';
                break;
        }
    }

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            case 1:
            default:
                return 'ثبت شده';
                break;
            case 2:
                return 'تایید شده';
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

    public function setGuaranteeStartAttribute($value)
    {
        if (!is_null($value)) {
            if (str_contains($value, '/')) {
                $format = 'Y/m/d';
            } elseif (str_contains($value, '-')) {
                $format = 'Y-m-d';
            }

            $this->attributes['guarantee_start'] = Jalalian::fromFormat($format, substr($value, 0, 10))->toCarbon();
        }
    }

    public function setGuaranteeEndAttribute($value)
    {
        if (!is_null($value)) {
            if (str_contains($value, '/')) {
                $format = 'Y/m/d';
            } elseif (str_contains($value, '-')) {
                $format = 'Y-m-d';
            }

            $this->attributes['guarantee_end'] = Jalalian::fromFormat($format, substr($value, 0, 10))->toCarbon();
        }
    }

    public function getGuaranteeStartAttribute()
    {
        if (!is_null($this->attributes['guarantee_start'])) {
            return Jalalian::forge($this->attributes['guarantee_start'])->format('Y/m/d');
        }
        return '';
    }

    public function getGuaranteeEndAttribute()
    {
        if (!is_null($this->attributes['guarantee_end'])) {
            return Jalalian::forge($this->attributes['guarantee_end'])->format('Y/m/d');
        }
        return '';
    }

    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class, 'device_type_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
