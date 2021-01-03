<?php

namespace App\Models\Repairs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Morilog\Jalali\Jalalian;

class Event extends Model
{
    use HasFactory;

    protected $table = 'repair_events';

    protected $fillable = ['user_id', 'repair_id', 'status', 'title', 'description'];

    protected $appends = ['jDate'];

    public function getJDateAttribute()
    {
        if (is_null($this->attributes['created_at'])) return null;
        return Jalalian::forge($this->attributes['created_at'])->format('Y/m/d H:i:s');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
