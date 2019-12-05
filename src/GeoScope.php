<?php

namespace Netsells\GeoScope;

use Illuminate\Database\Eloquent\Builder;
use Netsells\GeoScope\Config\ConfigManager;

class GeoScope
{
    const DISTANCE_UNITS_MILES = 'miles';
    const DISTANCE_UNITS_METERS = 'meters';
    const DISTANCE_UNITS_KILOMETERS = 'kilometers';

    const DISTANCE_CONVERSION_FROM_METERS = [
        self::DISTANCE_UNITS_MILES => 0.000621371,
        self::DISTANCE_UNITS_METERS => 1,
        self::DISTANCE_UNITS_KILOMETERS => 0.001
    ];

    protected $scopeDriver;
    protected $config;

    /**
     * GeoScope constructor.
     * @param Builder $query
     * @param null $configOption
     */
    public function __construct(Builder $query, $configOption = null)
    {
        $this->query = $query;

        $this->config = app(ConfigManager::class, [
            'query' => $query,
            'configOption' => $configOption,
        ])->getConfig();

        $this->setScopeDriver();
    }

    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     * @return mixed
     */
    public function withinDistanceOf(float $lat, float $long, float $distance): Builder
    {
        return $this->scopeDriver->withinDistanceOf($lat, $long, $distance);
    }

    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     * @return mixed
     */
    public function orWithinDistanceOf(float $lat, float $long, float $distance): Builder
    {
        return $this->scopeDriver->orWithinDistanceOf($lat, $long, $distance);
    }

    /**
     * @param $driver
     * @return $this
     */
    protected function setScopeDriver(): GeoScope
    {
        if (!array_key_exists('scope-driver', $this->config) || !$this->config['scope-driver']) {
            $driver = $this->query->getConnection()->getConfig('driver');
        } else {
            $driver = $this->config['scope-driver'];
        }

        $this->scopeDriver = app(ScopeDriverFactory::class)
            ->getStrategyInstance($driver)
            ->setQuery($this->query)
            ->setConversion(self::DISTANCE_CONVERSION_FROM_METERS[$this->config['units']])
            ->setConfig($this->config);

        return $this;
    }
}

