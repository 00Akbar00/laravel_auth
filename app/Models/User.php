<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['email', 'passwordHash', 'status'];

    public function verifications()
    {
        return $this->hasMany(UserVerification::class, 'userId');
    }

    public function tokens()
    {
        return $this->hasMany(PersonalAccessToken::class, 'userId');
    }

    public function sessions()
    {
        return $this->hasMany(Session::class, 'userId');
    }

    public function passwordResets()
    {
        return $this->hasMany(PasswordReset::class, 'userId');
    }
}
