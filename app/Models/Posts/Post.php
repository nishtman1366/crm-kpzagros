<?php

namespace App\Models\Posts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Morilog\Jalali\Jalalian;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'post_category_id', 'title', 'body', 'status'];

    protected $appends = ['date', 'statusText'];

    public function getDateAttribute()
    {
        return Jalalian::forge($this->attributes['created_at'])->format('Y/m/d H:i');
    }

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            default:
            case 1:
                return 'انتشار';
            case 0:
                return 'عدم انتشار';

        }
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'post_category_id', 'id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'post_id', 'id');
    }

    public function videos()
    {
        return $this->hasMany(Video::class, 'post_id', 'id');
    }

    public function levels()
    {
        return $this->hasMany(Level::class, 'post_id', 'id');
    }
}
