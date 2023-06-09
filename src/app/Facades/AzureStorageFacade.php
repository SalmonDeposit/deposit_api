<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AzureStorageFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'azure-storage';
    }

    protected static function getFacadeMiddleware()
    {
        return ['\App\Http\Middleware\AzureStorageMiddleware'];
    }
}
