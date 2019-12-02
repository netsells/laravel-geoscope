<?php

namespace Netsells\GeoScope\ScopeDrivers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Netsells\GeoScope\GeoScope;

class DefaultScopeDriver extends AbstractScopeDriver
{
    /**
     * @var array
     * Radius of the earth
     */
    private $circleRadius = [
        GeoScope::DISTANCE_UNITS_MILES => 3959,
        GeoScope::DISTANCE_UNITS_KILOMETERS => 6371.393,
        GeoScope::DISTANCE_UNITS_METERS => 6371393
    ];

    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     * @return Builder
     */
    public function withinDistanceOf(float $lat, float $long, float $distance): Builder
    {
        return $this->query->where($this->getDbDistanceStatement($lat, $long), '<=', $distance);
    }

    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     * @return Builder
     */
    public function orWithinDistanceOf(float $lat, float $long, float $distance): Builder
    {
        return $this->query->orWhere($this->getDbDistanceStatement($lat, $long), '<=', $distance);
    }

    /**
     * @param float $lat
     * @param float $long
     * @return string
     */
    private function getDistanceStatement(float $lat, float $long): string
    {
        $lat = floatval($lat);
        $long = floatval($long);

        return <<<SELECT
            ROUND (
              (
                {$this->circleRadius[$this->config['units']]}
                *
                acos (
                  cos(radians($lat)) * cos(radians({$this->config['lat-column']}))
				  * cos(radians({$this->config['long-column']}) - radians($long))
				  + sin(radians($lat)) * sin(radians({$this->config['lat-column']}))
				)
			  )
        	, 2)
SELECT;
    }

    /**
     * @param float $lat
     * @param float $long
     * @return \Illuminate\Database\Query\Expression
     */
    private function getDbDistanceStatement(float $lat, float $long): Expression
    {
        return DB::raw($this->getDistanceStatement($lat, $long));
    }
}
