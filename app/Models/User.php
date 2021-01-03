<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'mobile',
        'email',
        'password',
        'level',
        'parent_id',
        'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url', 'statusText', 'levelText'
    ];

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            case 0:
                return 'غیرفعال';
            case 1:
                return 'فعال';
            case 2:
                return 'معلق';
        }
    }


    public function getLevelTextAttribute()
    {
        switch ($this->attributes['level']) {
            case 'SUPERUSER':
                return 'مدیرکل';
            case 'ADMIN':
                return 'مدیر سیستم';
            case 'AGENT':
                return 'نماینده';
            case 'MARKETER':
                return 'بازاریاب';
            case 'TECHNICAL':
                return 'کارشناس فنی';
        }
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id', 'id');
    }

    public function isSuperUser()
    {
        if ($this->attributes['level'] == 'SUPERUSER') return true;

        return false;
    }

    public function isAdmin()
    {
        if ($this->attributes['level'] == 'ADMIN') return true;

        return false;
    }

    public function isAgent()
    {
        if ($this->attributes['level'] == 'AGENT') return true;

        return false;
    }

    public function isMarketer()
    {
        if ($this->attributes['level'] == 'MARKETER') return true;

        return false;
    }

    protected function defaultProfilePhotoUrl()
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }
}
