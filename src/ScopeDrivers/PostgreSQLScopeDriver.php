<?php

namespace Netsells\GeoScope\ScopeDrivers;

use Illuminate\Database\Eloquent\Builder;

final class PostgreSQLScopeDriver extends AbstractScopeDriver
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
            $lat,
            $long,
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
            $lat,
            $long,
            $distance,
        ]);
    }

    /**
     * @return string
     */
    private function getSQL(): string
    {
        return <<<EOD
            earth_distance(
                ll_to_earth({$this->config['lat-column']}, {$this->config['long-column']}),
                ll_to_earth(?, ?)
             ) * {$this->conversion} < ?
EOD;
    }
}
