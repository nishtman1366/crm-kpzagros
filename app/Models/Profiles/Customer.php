<?php

namespace App\Models\Profiles;

use App\Http\Controllers\Profiles\LicenseController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', 'user_id', 'profile_id', 'national_code', 'id_code', 'first_name', 'first_name_english',
        'last_name', 'last_name_english', 'father', 'father_english', 'gender', 'mobile',
        'birthday', 'company_name', 'company_name_english', 'business_name', 'reg_date',
        'reg_code', 'company_national_code'];

    protected $appends = ['fullName', 'genderText', 'typeText', 'jBirthday', 'jRegDate',
        'nationalCard1Url', 'nationalCard2Url', 'idCardUrl',
        'asasnamehUrl', 'agahi1Url', 'agahi2Url'];

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
}
