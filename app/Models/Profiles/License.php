<?php

namespace App\Models\Profiles;

use App\Models\Profiles\LicenseType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    protected $fillable = ['license_type_id', 'profile_id', 'account_id', 'name', 'file'];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        $version = $this->updated_at->timestamp;

        return url('storage') . '/profiles/' . $this->attributes['profile_id'] . '/' . $this->attributes['file'] . '?ver=' . $version;
    }

    public function type()
    {
        return $this->belongsTo(LicenseType::class, 'license_type_id', 'id');
    }
}
