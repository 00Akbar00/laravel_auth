<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVerification extends Model
{
    public $timestamps = false;

    protected $fillable = ['userId', 'verificationToken', 'expiresAt'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
