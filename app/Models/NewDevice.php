<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewDevice extends Model
{
    use HasFactory;

    protected $fillable = ['serial', 'device_type_id', 'imei', 'sim_number', 'national_code', 'status'];
}
