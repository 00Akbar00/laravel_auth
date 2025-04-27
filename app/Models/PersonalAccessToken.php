<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalAccessToken extends Model
{
    public $timestamps = false;

    protected $fillable = ['userId', 'tokenHash', 'expiresAt', 'lastUsedAt'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
