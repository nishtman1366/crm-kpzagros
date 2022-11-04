<?php

namespace App\Models\Repairs;

use App\Models\Payments\Payment;
use App\Models\User;
use App\Models\Variables\Accessory;
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
        $accessories = explode(',', $this->attributes['accessories']);
        $list = [];
        $items = Accessory::whereIn('id', $accessories)->get();
        foreach ($items as $item) {
            if (!is_null($item)) $list[] = $item->name;
        }
        return implode(' , ', $list);
    }

    public function getAccessoriesAttribute()
    {
        if (is_null($this->attributes['accessories'])) return null;

        return explode(',', $this->attributes['accessories']);
    }

    public function setAccessoriesAttribute($value)
    {
        if (!is_null($value) && is_array($value)) {
            $this->attributes['accessories'] = implode(',', $value);
        }
    }

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            case 1:
                return 'ثبت شده';
            case 2:
                return 'دریافت شده توسط واحد فنی';
            case 3:
                return 'در صف تعمیر';
            case 4:
                return 'تعمیر شده';
            case 5:
                return 'در انتظار پرداخت';
            case 6:
                return 'پرداخت شده';
            case 7:
                return 'عودت شده';
            case 8:
                return 'غیرقابل تعمیر';
            default:
                return 'ثبت موقت';
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
