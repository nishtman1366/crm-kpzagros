<?php

namespace App\Models\Variables;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSubCategory extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'code'];

    public function parent()
    {
        return $this->belongsTo(BusinessCategory::class,'category_id');
    }
}
