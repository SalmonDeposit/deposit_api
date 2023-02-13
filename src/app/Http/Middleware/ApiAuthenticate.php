<?php

namespace App\Http\Middleware;

use App\Http\Resources\V1\UserResource;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class ApiAuthenticate extends Middleware
{
    protected function authenticated(): UserResource
    {
        return new UserResource(auth()->user());
    }
}
