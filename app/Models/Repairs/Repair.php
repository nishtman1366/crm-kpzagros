<?php

namespace App\Models\Repairs;

use App\Models\Payments\Payment;
use App\Models\User;
use App\Models\Variables\Bank;
use App\Models\Variables\DeviceType;
use App\Models\Variables\Psp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Morilog\Jalali\Jalalian;

class Repair extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['user_id', 'device_type_id', 'psp_id', 'bank_id', 'location_id', 'serial', 'name', 'mobile', 'national_code', 'description', 'technical_description', 'status', 'tracking_code',
        'guarantee_end', 'price', 'new_serial', 'new_device_type_id', 'loan_device_type_id', 'loan_serial', 'deposit', 'business_name', 'accessories'];

    protected $appends = ['statusText', 'accessoryList', 'jCreatedAt', 'jUpdatedAt', 'jGuaranteeEnd'];

    public function getAccessoryListAttribute()
    {
        if (is_null($this->attributes['accessories'])) return null;

        return '';
    }

    public function getAccessoriesAttribute()
    {
        if (is_null($this->attributes['accessories'])) return null;

        return explode(',', $this->attributes['accessories']);
    }

    public function setAccessoriesAttribute($value)
    {
        $this->attributes['accessories'] = implode(',', $value);
    }

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            case 1:
                return 'ثبت شده';
                break;
            case 2:
                return 'دریافت شده توسط واحد فنی';
                break;
            case 3:
                return 'در صف تعمیر';
                break;
            case 4:
                return 'تعمیر شده';
                break;
            case 5:
                return 'در انتظار پرداخت';
                break;
            case 6:
                return 'پرداخت شده';
                break;
            case 7:
                return 'عودت شده';
                break;
            case 8:
                return 'غیرقابل تعمیر';
                break;
            default:
                return 'ثبت موقت';
                break;
        }
    }

    public function getJGuaranteeEndAttribute()
    {
        if (is_null($this->attributes['guarantee_end'])) return '';
        return Jalalian::forge($this->attributes['guarantee_end'])->format('Y/m/d');
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class, 'device_type_id', 'id');
    }

    public function psp()
    {
        return $this->belongsTo(Psp::class, 'psp_id', 'id');
    }


    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'repair_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'repair_id', 'id');
    }
}
