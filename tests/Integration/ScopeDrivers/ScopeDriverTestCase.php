<?php

namespace Netsells\GeoScope\Tests\Integration\ScopeDrivers;

use Netsells\GeoScope\GeoScope;
use Netsells\GeoScope\ScopeDriverFactory;
use Netsells\GeoScope\Tests\Integration\IntegrationTestCase;
use Netsells\GeoScope\Tests\Test;

abstract class ScopeDriverTestCase extends IntegrationTestCase
{
    protected $query;

    public function setUp(): void
    {
        parent::setUp();
        $this->query = factory(Test::class)->create()->query();
    }

    protected function getScopeDriver(string $driverKey, array $config = null)
    {
        if(!$config){
            $config = config('geoscope.defaults');
        }
        return app(ScopeDriverFactory::class)
            ->getStrategyInstance($driverKey)
            ->setQuery($this->query)
            ->setConversion(GeoScope::DISTANCE_CONVERSION_FROM_METERS[$config['units']])
            ->setConfig($config);
    }
}
