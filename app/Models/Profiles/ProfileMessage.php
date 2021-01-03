<?php

namespace App\Models\Profiles;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class ProfileMessage extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'profile_id', 'type', 'title', 'message'];

    protected $appends = ['color', 'jDate'];

    public function getJDateAttribute()
    {
//        return Jalalian::forge($this->attributes['created_at'])->format('Y/m/d H:i:s');
        return $this->created_at->diffForHumans();
    }

    public function getColorAttribute()
    {
        switch ($this->attributes['type']) {
            case 'INFO':
                return 'blue';
            case 'WARNING':
                return 'yellow';
            case 'DANGER':
                return 'red';
            case 'SUCCESS':
            default:
                return 'green';
        }
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
