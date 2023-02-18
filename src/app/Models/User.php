<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'email', 'password', 'simon_coin_stock'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
