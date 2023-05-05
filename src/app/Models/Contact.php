<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use Uuid;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'lastname', 'email', 'asked_question',
    ];

}
