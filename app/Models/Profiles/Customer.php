<?php

namespace App\Models\Profiles;

use App\Http\Controllers\Profiles\LicenseController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class Customer extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'type',
        'user_id',
        'profile_id',
        'national_code',
        'id_code',
        'first_name',
        'first_name_english',
        'last_name',
        'last_name_english',
        'father',
        'father_english',
        'gender',
        'mobile',
        'birthday',
        'company_name',
        'company_name_english',
        'business_name',
        'reg_date',
        'reg_code',
        'company_national_code',

        'vital',

        'residency',

        'country_code',
        'foreign_pervasive_code',
        'passport_number',
        'passport_expireDate',

        'birth_crtfct_serial',
        'birth_crtfct_series_letter',
        'birth_crtfct_series_number',

        'description',
        'phone',
        'email',
        'webSite',
        'fax',
    ];

    protected $appends = ['fullName', 'genderText', 'typeText', 'jBirthday', 'jRegDate',
        'nationalCard1Url', 'nationalCard2Url', 'idCardUrl',
        'asasnamehUrl', 'agahi1Url', 'agahi2Url', 'vitalText', 'residencyText', 'jPassportExpireDate', 'country'];

    public function getFullNameAttribute()
    {
        if ($this->attributes['type'] == 'PERSON') {
            return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
        } elseif ($this->attributes['type'] == 'ORGANIZATION') {
            return $this->attributes['company_name'] . ' (' . $this->attributes['first_name'] . ' ' . $this->attributes['last_name'] . ')';
        }
    }

    public function getGenderTextAttribute()
    {
        if ($this->attributes['gender'] == 'male') return 'مرد';
        else return 'زن';
    }

    public function getTypeTextAttribute()
    {
        if ($this->attributes['type'] == 'PERSON') return 'حقیقی';
        else return 'حقوقی';
    }

    public function getJBirthdayAttribute()
    {
        if (is_null($this->attributes['birthday'])) return '';
        return Jalalian::forge($this->attributes['birthday'])->format('Y/m/d');
    }

    public function getJPassportExpireDateAttribute()
    {
        return $this->attributes['passport_expireDate'];
    }

    public function getJRegDateAttribute()
    {
        if (is_null($this->attributes['reg_date'])) return '';
        return Jalalian::forge($this->attributes['reg_date'])->format('Y/m/d');
    }

    public function getNationalCard1UrlAttribute()
    {
        return LicenseController::view('national_card_file_1', $this->attributes['profile_id']);
    }

    public function getNationalCard2UrlAttribute()
    {
        return LicenseController::view('national_card_file_2', $this->attributes['profile_id']);

    }

    public function getIdCardUrlAttribute()
    {
        return LicenseController::view('id_file', $this->attributes['profile_id']);
    }

    public function getAsasnamehUrlAttribute()
    {
        return LicenseController::view('asasname_file', $this->attributes['profile_id']);
    }

    public function getAgahi1UrlAttribute()
    {
        return LicenseController::view('agahi_file_1', $this->attributes['profile_id']);
    }

    public function getAgahi2UrlAttribute()
    {
        return LicenseController::view('agahi_file_2', $this->attributes['profile_id']);
    }

    public function getVitalTextAttribute()
    {
        if (is_null($this->attributes['vital'])) return null;
        switch ($this->attributes['vital']) {
            default:
            case 'alive':
                return 'در قید حیات';
            case 'dead':
                return 'فوت شده';
        }
    }

    public function getResidencyTextAttribute()
    {
        if (is_null($this->attributes['vital'])) return null;
        switch ($this->attributes['vital']) {
            default:
            case 'iranian':
                return 'ایرانی';
            case 'foreign':
                return 'غیرایرانی';
        }
    }

    public function getCountryAttribute()
    {
        $country = DB::table('countries')->select(['country_name'])->where('country_code', $this->attributes['country_code'])->get(['country_name'])->first();
        if (!is_null($country)) return $country->country_name;

        return '';
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}
