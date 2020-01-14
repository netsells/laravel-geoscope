<?php

namespace Netsells\GeoScope\Tests\Integration\ScopeDrivers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Netsells\GeoScope\Tests\Integration\ScopeDrivers\Traits\ScopeDriverTests;

class PostgreSQLScopeDriverTest extends BaseScopeDriverTest
{
    use RefreshDatabase;
    use ScopeDriverTests;

    protected $scopeDriver;

    public function setUp(): void
    {
        parent::setUp();

        $this->scopeDriver = $this->getScopeDriver('pgsql');
        $this->sqlSnippet = "earth_distance";
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
            'driver' => 'pgsql',
            'host' => env('PGSQL_DB_HOST'),
            'database' => env('PGSQL_DB_DATABASE'),
            'username' => env('PGSQL_DB_USERNAME'),
            'password' => env('PGSQL_DB_PASSWORD'),
            'port' => env('PGSQL_DB_PORT'),
        ]);
    }
}
