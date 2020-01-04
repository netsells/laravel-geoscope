<?php

namespace Netsells\GeoScope\ScopeDrivers;

use Illuminate\Database\Eloquent\Builder;

final class SQLServerScopeDriver extends AbstractScopeDriver
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
            (GEOGRAPHY::Point(?, ?, 4326)
            .STDistance(GEOGRAPHY::Point({$this->config["lat-column"]}, {$this->config["long-column"]}, 4326))) 
            * {$this->conversion} < ?
EOD;
    }
}
