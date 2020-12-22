<?php

namespace Netsells\GeoScope\Tests\Integration\ScopeDrivers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Netsells\GeoScope\Tests\Integration\ScopeDrivers\Traits\ScopeDriverTests;

class MariaDbScopeDriverTest extends BaseScopeDriverTest
{
    use RefreshDatabase;
    use ScopeDriverTests;

    protected $scopeDriver;

    public function setUp(): void
    {
        parent::setUp();

        $this->scopeDriver = $this->getScopeDriver('mariadb');
        $this->sqlSnippet = "ST_Distance_Sphere";
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        // Setup default database to use mysql test
        $app['config']->set('database.connections.testing', [
            'driver' => 'mysql',
            'host' => env('MARIA_DB_HOST'),
            'database' => env('MARIA_DB_DATABASE'),
            'username' => env('MARIA_DB_USERNAME'),
            'password' => env('MARIA_DB_PASSWORD'),
            'port' => env('MARIA_DB_PORT'),
        ]);
    }
}
