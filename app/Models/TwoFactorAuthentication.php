<?php
// app/Models/TwoFactorAuthentication.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoFactorAuthentication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'secret',
        'recovery_codes',
        'verified_at',
        'enabled'
    ];

    protected $casts = [
        'recovery_codes' => 'array',
        'verified_at' => 'datetime',
        'enabled' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}