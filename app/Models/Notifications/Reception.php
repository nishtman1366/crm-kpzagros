<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reception extends Model
{
    use HasFactory;

    protected $table = 'notifications_receptions';

    protected $fillable = ['c', 'reception', 'status'];

    protected $appends=['statusText'];

    public function getStatusTextAttribute()
    {
        if($this->status===1) return 'ارسال موفق';
        return 'نامشخص';
    }
}
