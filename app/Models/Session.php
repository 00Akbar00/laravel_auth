<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    public $timestamps = false;

    protected $fillable = ['userId', 'sessionId', 'ipAddress', 'userAgent', 'metadata', 'expiresAt'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
