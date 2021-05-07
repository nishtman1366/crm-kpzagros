<?php

namespace App\Models\Payments;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['type_id', 'user_id', 'repair_id', 'return_device_id', 'profile_id', 'amount', 'reg_code', 'ref_code', 'status', 'tracking_code', 'date'];

    protected $appends = ['jDate', 'statusText'];

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            case 0:
                return 'ثبت موقت';
                break;
            case 1:
                return 'ثبت شده';
                break;
            case 2:
                return 'تایید شده';
                break;
        }
    }

    public function getJDateAttribute()
    {
        $date = is_null($this->attributes['date']) ? $this->attributes['created_at'] : $this->attributes['date'];
        return Jalalian::forge($date)->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function type()
    {
        return $this->hasOne(Type::class, 'id', 'type_id');
    }
}
