<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PspRequiredLicense extends Model
{
    use HasFactory;

    protected $fillable = ['psp_id', 'license_type_id'];
}
