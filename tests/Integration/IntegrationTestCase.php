<?php

namespace Netsells\GeoScope\Tests\Integration;

use Netsells\GeoScope\GeoScope;
use Netsells\GeoScope\ScopeDriverFactory;
use Netsells\GeoScope\Tests\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setGeoScopeConfig();
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->withFactories(__DIR__ . '/Database/Factories');
        $this->artisan('migrate', ['--database' => 'testing']);
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('database.default', 'testing');
    }
}
