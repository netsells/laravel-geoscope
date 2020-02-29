<?php

namespace Netsells\GeoScope;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;
use Netsells\GeoScope\BuilderScopes\DatabaseBuilderBuilderScope;

class GeoScopeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Config/geoscope.php' => config_path('geoscope.php'),
        ], 'geoscope');

        Builder::macro('withinDistanceOf', function (
            float $lat,
            float $long,
            float $distance,
            $configOption = null
        ) {
            return app(DatabaseBuilderBuilderScope::class, [
                'query' => $this,
                'configOption' => $configOption,
            ])->withinDistanceOf($lat, $long, $distance);
        });

        Builder::macro('orWithinDistanceOf', function (
            float $lat,
            float $long,
            float $distance,
            $configOption = null
        ) {
            return app(DatabaseBuilderBuilderScope::class, [
                'query' => $this,
                'configOption' => $configOption,
            ])->orWithinDistanceOf($lat, $long, $distance);
        });

        Builder::macro('orderByDistanceFrom', function (
            float $lat,
            float $long,
            $orderDirection = 'asc'
        ) {
            return app(DatabaseBuilderBuilderScope::class, [
                'query' => $this,
            ])->orderByDistanceFrom($lat, $long, $orderDirection);
        });
    }

    public function register()
    {
        $this->app->singleton(ScopeDriverFactory::class, function () {
            return new ScopeDriverFactory();
        });
    }
}
