<?php

namespace Netsells\GeoScope\Interfaces;

interface ScopeDriverInterface
{
    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     * @return mixed
     * Should return query instance
     */
    public function withinDistanceOf(float $lat, float $long, float $distance);

    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     * @return mixed
     * Should return query instance
     */
    public function orWithinDistanceOf(float $lat, float $long, float $distance);
}
