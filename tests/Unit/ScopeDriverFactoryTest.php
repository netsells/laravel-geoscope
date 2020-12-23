<?php

namespace Netsells\GeoScope\Tests\Unit;

use Mockery;
use Netsells\GeoScope\Interfaces\ScopeDriverInterface;
use Netsells\GeoScope\ScopeDriverFactory;
use Netsells\GeoScope\ScopeDrivers\MariaDbScopeDriver;
use Netsells\GeoScope\ScopeDrivers\MySQLScopeDriver;
use Netsells\GeoScope\ScopeDrivers\PostgreSQLScopeDriver;
use Netsells\GeoScope\ScopeDrivers\SQLServerScopeDriver;

class ScopeDriverFactoryTest extends UnitTestCase
{
    /**
     * @test
     */
    public function factory_can_create_mysql_driver()
    {
        $this->scopeDriverCreationTest(MySQLScopeDriver::class, 'mysql');
    }

    /**
     * @test
     */
    public function factory_can_create_postgres_driver()
    {
        $this->scopeDriverCreationTest(PostgreSQLScopeDriver::class, 'pgsql');
    }

    /**
     * @test
     */
    public function factory_can_create_sqlserver_driver()
    {
        $this->scopeDriverCreationTest(SQLServerScopeDriver::class, 'sqlsrv');
    }

    /**
     * @test
     */
    public function factory_can_create_mariadb_driver()
    {
        config()->set('database.connections.testing', [
            'driver' => 'mysql',
            'host' => env('MARIADB_DB_HOST'),
            'database' => env('MARIADB_DB_DATABASE'),
            'username' => env('MARIADB_DB_USERNAME'),
            'password' => env('MARIADB_DB_PASSWORD'),
            'port' => env('MARIADB_DB_PORT'),
        ]);

        $this->scopeDriverCreationTest(MariaDbScopeDriver::class, 'mariadb');
    }

    /**
     * @test
     */
    public function factory_can_register_a_custom_driver()
    {
        $factory = app(ScopeDriverFactory::class);

        $expected = get_class(Mockery::mock('CustomScopeDriver', ScopeDriverInterface::class));

        $factory->registerDriverStrategy('custom', $expected);

        $actual = get_class($factory->getStrategyInstance('custom'));

        $this->assertEquals($expected, $actual);
    }

    public function scopeDriverCreationTest(string $scopeDriverClassName, string $factoryKey)
    {
        $factory = app(ScopeDriverFactory::class);

        $actual = get_class($factory->getStrategyInstance($factoryKey));

        $this->assertEquals($scopeDriverClassName, $actual);
    }
}
