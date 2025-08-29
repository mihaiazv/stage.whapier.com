<?php

namespace Modules\RegistrationApi\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\RegistrationApi\Listeners\SendUserToApi;

class RegistrationApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/registration_api.php', 'registration_api');
    }

    public function boot(): void
    {
        Event::listen(Registered::class, [SendUserToApi::class, 'handle']);
    }
}
