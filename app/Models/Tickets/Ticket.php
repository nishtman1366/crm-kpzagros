<?php

namespace App\Models\Tickets;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Morilog\Jalali\Jalalian;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'user_id', 'agent_id', 'password', 'ticket_type_id', 'title', 'body', 'status'];

    protected $appends = ['statusText', 'createDate', 'updateDate'];

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            default:
            case 0:
                return 'ثبت شده';
            case 1:
                return 'در حال بررسی';
            case 2:
                return 'پاسخ داده شده';
            case 3:
                return 'پاسخ کاربر';
            case 4:
                return 'منتقل شده';
            case 99:
                return 'پایان یافته';
        }
    }

    public function getCreateDateAttribute()
    {
        if (is_null($this->attributes['created_at'])) return null;
        if (now()->subHours(8)->lt($this->created_at)) {
            return $this->created_at->diffForHumans();
        } else {
            return Jalalian::forge($this->created_at)->format('Y/m/d H:i:s');
        }
    }

    public function getUpdateDateAttribute()
    {
        if (is_null($this->attributes['updated_at'])) return null;

        if (now()->subHours(8)->lt($this->updated_at)) {
            return $this->updated_at->diffForHumans();
        } else {
            return Jalalian::forge($this->updated_at)->format('Y/m/d H:i:s');
        }
    }

    public function setPasswordAttribute($value)
    {
        if (!is_null($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    public function agent()
    {
        return $this->hasOne(Agent::class, 'id', 'agent_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function type()
    {
        return $this->hasOne(Type::class, 'id', 'ticket_type_id');
    }

    public function replies()
    {
        return $this->hasMany(Reply::class, 'ticket_id', 'id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'ticket_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'ticket_id', 'id');
    }
}
