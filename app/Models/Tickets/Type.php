<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $table = 'ticket_types';

    protected $fillable = ['name', 'status'];

    protected $appends = ['statusText'];

    public function getStatusTextAttribute()
    {
        if (!is_null($this->attributes['status']) && $this->attributes['status'] === true) return 'فعال';
        return 'غیرفعال';
    }

    public function agents()
    {
        return $this->hasMany(Agent::class, 'ticket_type_id', 'id');
    }
}
