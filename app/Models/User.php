<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'passwordHash', 'status'];

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function tokens()
    {
        return $this->hasMany(PersonalAccessToken::class);
    }

    public function passwordResets()
    {
        return $this->hasMany(PasswordReset::class);
    }
}

