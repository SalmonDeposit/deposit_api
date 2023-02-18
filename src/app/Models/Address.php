<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    use HasFactory, Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'addresses';

    protected $fillable = [
        'line', 'postal_code', 'city', 'country', 'is_default'
    ];

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }
}
