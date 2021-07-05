<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Event extends Model
{
    use HasFactory;

    protected $table = 'ticket_events';

    protected $fillable = ['ticket_id', 'title', 'body'];

    protected $appends = ['date'];

    public function getDateAttribute()
    {
        if (is_null($this->attributes['created_at'])) return null;

        return Jalalian::forge($this->created_at)->format('Y/m/d H:i:s');
    }
}
