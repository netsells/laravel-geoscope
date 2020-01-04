<?php

namespace Netsells\GeoScope;

use Illuminate\Support\ServiceProvider;

class GeoScopeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Config/geoscope.php' => config_path('geoscope.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton(ScopeDriverFactory::class, function () {
            return new ScopeDriverFactory();
        });
    }
}
