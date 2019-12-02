<?php

namespace Netsells\GeoScope;

use Netsells\GeoScope\Interfaces\ScopeDriverInterface;
use Netsells\GeoScope\ScopeDrivers\DefaultScopeDriver;
use Netsells\GeoScope\ScopeDrivers\MySQLScopeDriver;

class ScopeDriverFactory
{
    /**
     * @var array
     */
    protected $registeredStrategies = [
        'default' => DefaultScopeDriver::class,
        'mysql' => MySQLScopeDriver::class,
    ];

    /**
     * @param $key
     * @return ScopeDriverInterface
     */
    public function getStrategyInstance($key): ScopeDriverInterface
    {
        if (array_key_exists($key, $this->registeredStrategies)) {
            $strategy = new $this->registeredStrategies[$key];
            return $strategy;
        }

        // If there is no driver registered for the DB
        // Then we'll fall back to the default
        return new $this->registeredStrategies['default'];
    }

    /**
     * @param string $key
     * @param ScopeDriverInterface $strategy
     */
    public function registerDriverStrategy(string $key, string $strategy): void
    {
        $this->registeredStrategies[$key] = $strategy;
    }
}
