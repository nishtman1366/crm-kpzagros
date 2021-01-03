<?php

namespace App\Models\Variables;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevicePsp extends Model
{
    use HasFactory;

    protected $fillable = ['device_type_id', 'psp_id'];

    public $timestamps = false;
}
