<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Session extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string'; // UUID

    protected $fillable = [
        'id', 'user_id', 'ip_address', 'user_agent', 'metadata', 'expires_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

