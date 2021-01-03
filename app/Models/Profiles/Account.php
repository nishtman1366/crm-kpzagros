<?php

namespace App\Models\Profiles;

use App\Http\Controllers\Profiles\LicenseController;
use App\Models\Profiles\License;
use App\Models\Variables\Bank;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'bank_id', 'branch', 'account_number', 'sheba_code', 'first_name', 'last_name', 'national_code', 'mobile', 'birthday'];

    protected $appends = ['fullName', 'shebaText', 'jBirthday', 'shebaFile'];

    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }

    public function getShebaTextAttribute()
    {
        return 'IR' . $this->attributes['sheba_code'];
    }

    public function getJBirthdayAttribute()
    {
        if (is_null($this->attributes['birthday'])) return '';
        return Jalalian::forge($this->attributes['birthday'])->format('Y/m/d');
    }

    public function getShebaFileAttribute()
    {
        return LicenseController::view('sheba_file', $this->profile->profile_id, $this->attributes['id']);
    }

    public function bank()
    {
        return $this->hasOne(Bank::class, 'id', 'bank_id');
    }

    public function profile()
    {
        return $this->belongsTo(ProfilesAccount::class, 'id', 'account_id');
    }
}
