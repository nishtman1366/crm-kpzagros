<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $connection = 'email';
    protected $table = 'Users_tbl';
    protected $primaryKey = 'UserId';
    protected $fillable = ['DomainId', 'password', 'Email', 'owner', 'status'];
    protected $appends = ['statusText'];

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            case 0:
                return 'غیرفعال';
            case 1:
                return 'فعال';
        }
    }
}
