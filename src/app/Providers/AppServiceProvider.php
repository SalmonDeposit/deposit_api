<?php

namespace App\Providers;

use App\Services\AzureStorage;
use App\Services\GoogleOAuth;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('azure-storage', function($app) {
            return new AzureStorage();
        });

        $this->app->singleton('google-oauth', function($app) {
            return new GoogleOAuth();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();
    }
}
