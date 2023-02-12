<?php

namespace App\Models\Profiles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class UserUploadedLicense extends Model
{
    use HasFactory;

    protected $fillable = ['profile_id', 'status'];
    protected $appends = ['statusText', 'jCreatedAt', 'jUpdatedAt'];

    public function getStatusTextAttribute(): string
    {
        return $this->status === 0 ? 'ارسال شده' : 'بررسی شده';
    }

    public function getJCreatedAtAttribute()
    {
        if (is_null($this->attributes['created_at'])) return '';
        return Jalalian::forge($this->attributes['created_at'])->format('Y/m/d H:i:s');
    }

    public function getJUpdatedAtAttribute()
    {
        if (is_null($this->attributes['updated_at'])) return '';
        return Jalalian::forge($this->attributes['updated_at'])->format('Y/m/d H:i:s');
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
