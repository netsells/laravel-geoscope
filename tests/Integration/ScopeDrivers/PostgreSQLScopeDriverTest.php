<?php

namespace Netsells\GeoScope\Tests\Integration\ScopeDrivers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Netsells\GeoScope\Tests\Integration\ScopeDrivers\Traits\ScopeDriverDatabaseTests;

class PostgreSQLScopeDriverTest extends BaseScopeDriverTest
{
    use RefreshDatabase, ScopeDriverDatabaseTests;

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
            'host' => 'localhost',
            'database' => env('PGSQL_DB_DATABASE'),
            'username' => env('PGSQL_DB_USERNAME'),
            'password' => env('PGSQL_DB_PASSWORD'),
        ]);
    }
}
