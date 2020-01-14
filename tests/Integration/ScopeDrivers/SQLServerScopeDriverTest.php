<?php

namespace Netsells\GeoScope\Tests\Integration\ScopeDrivers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Netsells\GeoScope\Tests\Integration\ScopeDrivers\Traits\ScopeDriverTests;

class SQLServerScopeDriverTest extends BaseScopeDriverTest
{
    use RefreshDatabase;
    use ScopeDriverTests;

    protected $scopeDriver;

    public function setUp(): void
    {
        parent::setUp();

        $this->scopeDriver = $this->getScopeDriver('sqlsrv');
        $this->sqlSnippet = "STDistance";
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
            'driver' => 'sqlsrv',
            'host' => env('SQLSRV_DB_HOST'),
            'database' => env('SQLSRV_DB_DATABASE'),
            'username' => env('SQLSRV_DB_USERNAME'),
            'password' => env('SQLSRV_DB_PASSWORD'),
            'port' => env('SQLSRV_DB_PORT'),
            'prefix' => '',
        ]);
    }
}
