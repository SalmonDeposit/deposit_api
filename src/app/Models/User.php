<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Uuid;
use App\Scopes\DeletedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use Uuid;
    use Billable;

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

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new DeletedScope);
    }

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
        return $this->hasMany(Document::class, 'user_id')->orderBy('name');
    }

    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class, 'user_id')->orderBy('name');
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

    public function anonymize(): void
    {
        $this->update([
            'email' => 'DELETED@'.Hash::make(random_bytes(36)),
            'remember_token' => null,
            'is_admin' => 0,
            'email_verified_at' => null,
            'password' => 'DELETED',
            'deleted' => true
        ]);
        $this->tokens()->delete();
    }
}
