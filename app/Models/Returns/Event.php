<?php

namespace App\Models\Returns;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Event extends Model
{
    use HasFactory;

    protected $table = 'returns_events';

    protected $fillable = ['user_id', 'return_device_id', 'status', 'title', 'description'];

    protected $appends = ['jDate'];

    public function getJDateAttribute()
    {
        if (is_null($this->attributes['created_at'])) return null;
        return Jalalian::forge($this->attributes['created_at'])->format('Y/m/d H:i:s');
    }

    public function getDescriptionAttribute()
    {
        if (is_null($this->attributes['description'])) return null;

        return nl2br($this->attributes['description']);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
