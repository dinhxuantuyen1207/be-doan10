<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use App\Observers\UserObserver;
use App\Events\YourEvent; // Thêm sự kiện của bạn vào đây
use App\Listeners\YourEventListener; // Thêm lắng nghe của bạn vào đây

class AppServiceProvider extends ServiceProvider
{
    // In App\Providers\EventServiceProvider

    protected $listen = [
        'App\Events\YourEvent' => [
            'App\Listeners\YourListener',
        ],
    ];

    public function boot()
    {
        parent::boot();

        Event::listen(
            'Illuminate\Mail\Events\MessageSending',
            function ($event) {
                // Your closure logic here
            }
        );
    }
}
