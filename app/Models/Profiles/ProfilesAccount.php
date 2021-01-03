<?php

namespace App\Models\Profiles;

use App\Models\Profiles\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilesAccount extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['profile_id', 'account_id'];

    public function account()
    {
        return $this->hasOne(Account::class, 'id', 'account_id');
    }
}
