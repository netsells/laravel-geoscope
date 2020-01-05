<?php

namespace Netsells\GeoScope\BuilderScopes;

use Netsells\GeoScope\ScopeDriverFactory;
use Netsells\GeoScope\Validators\TableFieldValidator;

abstract class AbstractBuilderScope
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
    protected $query;
    protected $table;

    /**
     * AbstractBuilderScope constructor.
     * @throws \Netsells\GeoScope\Exceptions\InvalidConfigException
     */
    public function __construct()
    {
        $this->checkValidLatLongColumns($this->table);
        $this->setScopeDriver();
    }

    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     * @return mixed
     */
    public function withinDistanceOf(float $lat, float $long, float $distance)
    {
        return $this->scopeDriver->withinDistanceOf($lat, $long, $distance);
    }

    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     * @return mixed
     */
    public function orWithinDistanceOf(float $lat, float $long, float $distance)
    {
        return $this->scopeDriver->orWithinDistanceOf($lat, $long, $distance);
    }

    /**
     * @param $driver
     * @return $this
     */
    protected function setScopeDriver(): AbstractBuilderScope
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

    /**
     * @param string $table
     * @throws \Netsells\GeoScope\Exceptions\InvalidConfigException
     */
    private function checkValidLatLongColumns(string $table): void
    {
        TableFieldValidator::validate($table, $this->config['lat-column']);
        TableFieldValidator::validate($table, $this->config['long-column']);
    }
}
