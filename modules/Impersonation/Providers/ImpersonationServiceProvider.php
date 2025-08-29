<?php

namespace Modules\Impersonation\Providers;

use Illuminate\Support\ServiceProvider;

class ImpersonationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
