<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'email', 'password', 'simon_coin_stock'
    ];

    protected $hidden = [
        'password', 'remember_token', 'is_admin'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function socials(): HasMany
    {
        return $this->hasMany(Social::class, 'user_id');
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class, 'user_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'user_id');
    }

    public function createToken(string $name, array $abilities = ['*']): NewAccessToken
    {
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(40)),
            'abilities' => $abilities,
            'expired_at' => now()->addMinutes(config('sanctum.expiration'))
        ]);

        return new NewAccessToken($token, $token->getKey().'|'.$plainTextToken);
    }

    /**
     * Returns MySQL 'users' tables columns name
     * Used to simplify the writing of headers in CSV files
     *
     * @return array
     */
    public static function getTableColumns(): array
    {
        return Schema::getColumnListing('users');
    }
}
