<?php

namespace App\Models\Variables;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceConnectionType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
}
