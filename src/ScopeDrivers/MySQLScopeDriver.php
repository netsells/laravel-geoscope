<?php

namespace Netsells\GeoScope\ScopeDrivers;

use Illuminate\Database\Eloquent\Builder;

class MySQLScopeDriver extends AbstractScopeDriver
{
    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     * @return Builder
     */
    public function withinDistanceOf(float $lat, float $long, float $distance): Builder
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
     * @return Builder
     */
    public function orWithinDistanceOf(float $lat, float $long, float $distance): Builder
    {
        return $this->query->orWhereRaw($this->getSQL(), [
            $long,
            $lat,
            $distance,
        ]);
    }

    /**
     * @return string
     */
    protected function getSQL(): string
    {
        return <<<EOD
            ST_Distance_Sphere(
                    point({$this->config['long-column']}, {$this->config['lat-column']}),
                    point(?, ?)
                ) * {$this->conversion} < ?
EOD;
    }
}
