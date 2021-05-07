<?php

namespace App\Models\Returns;

use App\Models\Payments\Payment;
use App\Models\User;
use App\Models\Variables\Accessory;
use App\Models\Variables\DeviceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class ReturnDevice extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = ['user_id', 'device_type_id', 'serial', 'amount', 'name', 'national_code', 'mobile', 'accessories', 'file', 'tracking_code', 'description', 'status'];

    protected $appends = ['statusText', 'accessoryList', 'jCreatedAt', 'jUpdatedAt', 'fileUrl'];

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
                break;
            case 2:
                return 'دریافت شده توسط واحد فنی';
                break;
            case 3:
                return 'در صف امور مالی';
                break;
            case 4:
                return 'پرداخت هزینه به خریدار';
                break;
            case 5:
                return 'عودت شده';
                break;
            case 6:
                return 'رد درخواست عودت';
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

    public function getFileUrlAttribute()
    {
        if (is_null($this->attributes['file'])) return null;

        return url('storage') . '/returns/faktors/' . $this->id . '/' . $this->attributes['file'];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function deviceType()
    {
        return $this->belongsTo(DeviceType::class, 'device_type_id', 'id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'return_device_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'return_device_id', 'id');
    }
}
