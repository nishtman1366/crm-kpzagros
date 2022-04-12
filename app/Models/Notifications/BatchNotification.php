<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class BatchNotification extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'pattern', 'parameters', 'type', 'body', 'status'];

    protected $appends = ['parametersList', 'statusText', 'createDate', 'updateDate'];

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            case 0:
                return 'ثبت شده';
                break;
            case 1:
                return 'ارسال شده';
                break;
            case 2:
                return 'در انتظار تایید';
                break;
            case 3:
                return 'در حال ارسال';
                break;
            case 4:
                return 'پایان یافته';
                break;
            case 5:
                return 'لغو شده';
                break;
        }
        return 'نام مشخص';
    }

    public function getCreateDateAttribute()
    {
        if (is_null($this->attributes['created_at'])) return null;

        return Jalalian::forge($this->created_at)->format('Y/m/d h:i:s');
    }

    public function getUpdateDateAttribute()
    {
        if (is_null($this->attributes['updated_at'])) return null;

        return Jalalian::forge($this->updated_at)->format('Y/m/d h:i:s');
    }

    public function getParametersListAttribute()
    {
        $parameters = $this->attributes['parameters'];
        if (is_null($parameters)) return null;
        $items = [];
        $list = explode(',', $parameters);
        foreach ($list as $item) {
            $value = explode('=', $item);
            $items[trim($value[0])] = trim($value[1]);
        }

        return $items;
    }

    public function receptions()
    {
        return $this->hasMany(Reception::class, 'batch_notification_id', 'id');
    }
}
