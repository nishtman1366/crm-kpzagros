<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'notification_events';

    protected $fillable = ['event_type', 'notification_type_id', 'event', 'level', 'status'];

    protected $appends = ['statusText', 'levelText', 'eventTypeText'];

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

    public function getLevelTextAttribute()
    {
        switch ($this->attributes['level']) {
            case 'SUPERUSER':
                return 'مدیرکل';
                break;
            case 'ADMIN':
                return 'مدیرسیستم';
                break;
            case 'AGENT':
                return 'نمایندگان';
                break;
            case 'MARKETER':
                return 'بازاریابان';
                break;
            case 'CUSTOMER':
                return 'پذیرندگان';
                break;
            case 'TECHNICAL':
                return 'کارشناسان فنی';
                break;
        }
    }

    public function getEventTypeTextAttribute()
    {
        switch ($this->attributes['event_type']) {
            case 'PROFILES':
                return 'پرونده ها';
                break;
            case 'REPAIRS':
                return 'تعمیرات';
                break;
            case 'DEVICES':
                return 'دستگاه ها';
                break;
        }
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'notification_type_id', 'id');
    }
}
