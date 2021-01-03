<?php

namespace App\Models\Variables;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'device_connection_type_id', 'image', 'description', 'status'];

    protected $appends = ['statusText'];

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            default:
            case 1:
                return 'فعال';
            case 2:
                return 'غیرفعال';
        }
    }

    public function type()
    {
        return $this->belongsTo(DeviceConnectionType::class, 'device_connection_type_id', 'id');
    }

    public function devices()
    {
        return $this->hasMany(Device::class, 'device_type_id', 'id');
    }

    public function psps()
    {
        return $this->hasMany(DevicePsp::class, 'device_type_id', 'id');
    }
}
