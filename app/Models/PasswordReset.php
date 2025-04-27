<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    public $timestamps = false;

    protected $fillable = ['userId', 'otpHash', 'ipAddress', 'expiresAt'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
