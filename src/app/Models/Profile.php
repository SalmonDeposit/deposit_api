<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Profile extends Model
{
    use HasFactory;
    use Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'address_id', 'firstname', 'lastname', 'email', 'phone_number',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }
}
