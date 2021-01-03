<?php

namespace App\Models\Profiles;

use App\Models\Profiles\LicenseType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    protected $fillable = ['license_type_id', 'profile_id', 'account_id', 'name', 'file'];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        $microtime = microtime();
        $version = explode(' ', $microtime);
        return url('storage') . '/profiles/' . $this->attributes['profile_id'] . '/' . $this->attributes['file'] . '?ver=' . $version[1];
    }

    public function type()
    {
        return $this->belongsTo(LicenseType::class, 'license_type_id', 'id');
    }
}
