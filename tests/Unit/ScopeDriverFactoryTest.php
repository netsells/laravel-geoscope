<?php

namespace Netsells\GeoScope\Tests\Unit;

use Mockery;
use Netsells\GeoScope\Interfaces\ScopeDriverInterface;
use Netsells\GeoScope\ScopeDriverFactory;
use Netsells\GeoScope\ScopeDrivers\DefaultScopeDriver;
use Netsells\GeoScope\ScopeDrivers\MySQLScopeDriver;

class ScopeDriverFactoryTest extends UnitTestCase
{
    /**
     * @test
     */
    public function factory_can_create_mysql_driver()
    {
        $factory = app(ScopeDriverFactory::class);

        $expected = MySQLScopeDriver::class;

        $actual = get_class($factory->getStrategyInstance('mysql'));

        $this->assertEquals($expected, $actual);
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
}
