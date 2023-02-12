<?php

namespace App\Models\Profiles;

use App\Models\Profiles\LicenseType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class License extends Model
{
    use HasFactory;

    protected $fillable = ['license_type_id', 'profile_id', 'account_id', 'name', 'file', 'disk', 'status'];

    protected $appends = ['url', 'statusText'];

    public function getStatusTextAttribute(): string
    {
        return $this->status === 0 ? 'بارگذاری‌شده از طریق سامانه جامع' : 'بارگذاری‌شده از طریق اپلیکیشن';
    }

    public function getUrlAttribute()
    {
        $version = $this->updated_at->timestamp;
        return Storage::disk($this->disk)->url(sprintf('profiles/%s/%s?ver=%s', $this->attributes['profile_id'], $this->attributes['file'], $version));
//        return url('storage') . '/profiles/' . $this->attributes['profile_id'] . '/' . $this->attributes['file'] . '?ver=' . $version;
    }

    public function type()
    {
        return $this->belongsTo(LicenseType::class, 'license_type_id', 'id');
    }
}
