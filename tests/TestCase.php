<?php


namespace Netsells\GeoScope\Tests;


use Netsells\GeoScope\GeoScopeServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [GeoScopeServiceProvider::class];
    }

    protected function setGeoScopeConfig(array $config = null, $model = null)
    {
        $defaultConfig = [
            'lat-column' => 'latitude',
            'long-column' => 'longitude',
            'units' => 'miles' // miles, kilometers or meters,
        ];

        if (!$config && !$model) {
            return $this->app->config->set('geoscope.defaults', $defaultConfig);
        }

        if (!$model) {
            return $this->app->config->set('geoscope.defaults', $config);
        }

        $modelName = get_class($model);

        $this->app->config->set("geoscope.defaults", $defaultConfig);
        $this->app->config->set("geoscope.models.{$modelName}", $config);

    }


}
