<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reception extends Model
{
    use HasFactory;

    protected $table = 'notifications_receptions';

    protected $fillable = ['batch_notification_id', 'reception', 'status'];
}
