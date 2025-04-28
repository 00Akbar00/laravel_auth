<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
class PersonalAccessToken extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'token_hash', 'expires_at', 'last_used_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

