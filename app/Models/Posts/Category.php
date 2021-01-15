<?php

namespace App\Models\Posts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'post_categories';

    protected $fillable = ['name'];

    public function posts()
    {
        return $this->hasMany(Post::class, 'post_category_id', 'id');
    }
}
