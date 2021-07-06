<?php

namespace App\Models\Tickets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'ticket_files';

    protected $fillable = ['ticket_id', 'reply_id', 'name', 'size'];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        if (is_null($this->reply_id)) {
            return url('storage') . sprintf('/tickets/%s/%s', $this->ticket_id, $this->attributes['name']);
        } else {
            return url('storage') . sprintf('/tickets/%s/replies/%s/%s', $this->ticket_id, $this->reply_id, $this->attributes['name']);
        }
    }
}
