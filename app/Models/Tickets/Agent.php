<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $table = 'ticket_agents';

    protected $fillable = ['user_id', 'ticket_type_id', 'status', 'name'];

    protected $appends = ['statusText'];

    public function getStatusTextAttribute()
    {
        if (!is_null($this->attributes['status']) && $this->attributes['status'] === true) return 'فعال';
        return 'غیرفعال';
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'ticket_type_id', 'id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'agent_id', 'id');
    }

    public function openTickets()
    {
        return $this->hasMany(Ticket::class, 'agent_id', 'id')->where('status', '!=', 99);
    }
}
