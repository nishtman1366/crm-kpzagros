<?php

namespace App\Models\Posts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $table = 'post_videos';

    protected $fillable = ['post_id', 'name'];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return url(sprintf('storage/posts/%s/videos/%s', $this->attributes['post_id'], $this->attributes['name']));
    }
}
