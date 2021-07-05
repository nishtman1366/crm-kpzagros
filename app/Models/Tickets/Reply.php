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

        return Jalalian::forge($this->created_at)->format('Y/m/d h:i:s');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
