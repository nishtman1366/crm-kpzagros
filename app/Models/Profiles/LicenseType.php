<?php

namespace App\Models\Profiles;

use App\Models\Settings\PspRequiredLicense;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'key', 'file_name', 'required', 'status','merchant_type'];

    protected $appends = ['statusText', 'requireText'];

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            case 1:
            default:
                return 'فعال';
                break;
            case 0:
                return 'غیرفعال';
                break;
        }
    }

    public function getRequireTextAttribute()
    {
        switch ($this->attributes['required']) {
            case 1:
            default:
                return 'دارد';
                break;
            case 0:
                return 'ندارد';
                break;
        }
    }

    public function psps()
    {
        return $this->hasMany(PspRequiredLicense::class);
    }
}
