<?php

namespace App\Models\Tickets;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Reply extends Model
{
    use HasFactory;

    protected $table = 'tickets_replies';

    protected $fillable = ['user_id', 'ticket_id', 'body'];

    protected $appends = ['createDate'];


    public function getCreateDateAttribute()
    {
        if (is_null($this->attributes['created_at'])) return null;
        if (now()->subHours(8)->lt($this->created_at)) {
            return $this->created_at->diffForHumans();
        } else {
            return Jalalian::forge($this->created_at)->format('Y/m/d H:i:s');
        }
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'reply_id', 'id');
    }
}
