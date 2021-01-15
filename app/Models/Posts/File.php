<?php

namespace App\Models\Posts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'post_files';

    protected $fillable = ['post_id', 'name'];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return url(sprintf('storage/posts/%s/files/%s', $this->attributes['post_id'], $this->attributes['name']));
    }
}
