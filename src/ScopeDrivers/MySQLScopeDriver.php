<?php

namespace Netsells\GeoScope\ScopeDrivers;

final class MySQLScopeDriver extends AbstractScopeDriver
{
    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     */
    public function withinDistanceOf(float $lat, float $long, float $distance)
    {
        return $this->query->whereRaw($this->getSQL(), [
            $long,
            $lat,
            $distance,
        ]);
    }

    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     */
    public function orWithinDistanceOf(float $lat, float $long, float $distance)
    {
        return $this->query->orWhereRaw($this->getSQL(), [
            $long,
            $lat,
            $distance,
        ]);
    }

    /**
     * @param float $lat
     * @param float $long
     * @param float $orderDirection
     */
    public function orderByDistanceFrom(float $lat, float $long, string $orderDirection = 'asc')
    {
        return $this->query;
    }

    /**
     * @return string
     */
    private function getSQL(): string
    {
        return <<<EOD
            ST_Distance_Sphere(
                    point({$this->config['long-column']}, {$this->config['lat-column']}),
                    point(?, ?)
                ) * {$this->conversion} < ?
EOD;
    }
}
