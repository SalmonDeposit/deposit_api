<?php

namespace App\Providers;

use App\Models\Document;
use App\Models\Folder;
use App\Models\Profile;
use App\Observers\DocumentObserver;
use App\Observers\FolderObserver;
use App\Observers\ProfileObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Document::observe(DocumentObserver::class);
        Folder::observe(FolderObserver::class);
        Profile::observe(ProfileObserver::class);
    }
}
