<?php

namespace App\Models\Profiles;

use App\Http\Controllers\Profiles\LicenseController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class Business extends Model
{
    use HasFactory;

    protected $fillable = ['profile_id',
        'ostan_id', 'shahrestan_id', 'bakhsh_id', 'shahr_id',
        'dehestan_id', 'abadi_id', 'phone_code', 'senf', 'name', 'name_english',
        'address', 'postal_code', 'phone', 'has_license', 'license_code', 'license_date'];

    protected $appends = ['ostan', 'shahrestan', 'bakhsh', 'shahr', 'fullPhone', 'jLicenseDate', 'licenseFile', 'esteshhadFile'];

    public function getOstanAttribute()
    {
        $ostan = DB::table('ostan')->select(['name'])->where('id', $this->attributes['ostan_id'])->get(['name'])->first();
        if (!is_null($ostan)) return $ostan->name;

        return '';
    }

    public function getShahrestanAttribute()
    {
        $shahrestan = DB::table('shahrestan')->select(['name'])->where('id', $this->attributes['shahrestan_id'])->get(['name'])->first();
        if (!is_null($shahrestan)) return $shahrestan->name;

        return '';
    }

    public function getBakhshAttribute()
    {
        $bakhsh = DB::table('bakhsh')->select(['name'])->where('id', $this->attributes['bakhsh_id'])->get(['name'])->first();
        if (!is_null($bakhsh)) return $bakhsh->name;

        return '';
    }

    public function getShahrAttribute()
    {
        $shahr = DB::table('shahr')->select(['name'])->where('id', $this->attributes['shahr_id'])->get(['name'])->first();
        if (!is_null($shahr)) return $shahr->name;

        return '';
    }

    public function getFullPhoneAttribute()
    {
        return $this->attributes['phone_code'] . '' . $this->attributes['phone'];
    }

    public function getJLicenseDateAttribute()
    {
        if (is_null($this->attributes['license_date'])) return '';
        return Jalalian::forge($this->attributes['license_date'])->format('Y/m/d');
    }

    public function getLicenseFileAttribute()
    {
        return LicenseController::view('license_file', $this->attributes['profile_id']);
    }

    public function getEsteshhadFileAttribute()
    {
        return LicenseController::view('esteshhad_file', $this->attributes['profile_id']);
    }
}
