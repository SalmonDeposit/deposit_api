<?php

declare(strict_types=1);

namespace App\Models;

use App\Scopes\DeletedScope;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'address_id', 'firstname', 'lastname', 'email', 'phone_number', 'deleted'
    ];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new DeletedScope);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function anonymize(): void
    {
        $this->update([
            'firstname' => 'DELETED',
            'lastname' => 'DELETED',
            'email' => 'DELETED',
            'phone_number' => 'DELETED',
            'deleted' => 1
        ]);
    }
}
