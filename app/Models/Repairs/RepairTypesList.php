<?php

namespace App\Models\Repairs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairTypesList extends Model
{
    use HasFactory;

    protected $fillable = ['repair_id', 'type_id'];

    public $timestamps = false;
}
