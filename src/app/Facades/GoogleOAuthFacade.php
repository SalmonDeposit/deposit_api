<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class GoogleOAuthFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'google-oauth';
    }
}
