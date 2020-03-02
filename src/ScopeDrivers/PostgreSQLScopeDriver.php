<?php

namespace Netsells\GeoScope\ScopeDrivers;

final class PostgreSQLScopeDriver extends AbstractScopeDriver
{
    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     */
    public function withinDistanceOf(float $lat, float $long, float $distance)
    {
        return $this->query->whereRaw($this->getWithinDistanceSQL(), [
            $lat,
            $long,
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
        return $this->query->orWhereRaw($this->getWithinDistanceSQL(), [
            $lat,
            $long,
            $distance,
        ]);
    }

    /**
     * @throws InvalidOrderDirectionParameter
     * @param float $lat
     * @param float $long
     * @param float $orderDirection
     */
    public function orderByDistanceFrom(float $lat, float $long, string $orderDirection = 'asc')
    {
        $this->checkOrderDirectionIdentifier($orderDirection);

        return $this->query->orderByRaw($this->getOrderByDistanceSQL($orderDirection), [
            $lat,
            $long,
        ]);
    }

    /**
     * @return string
     */
    private function getWithinDistanceSQL(): string
    {
        return <<<EOD
            earth_distance(
                ll_to_earth({$this->config['lat-column']}, {$this->config['long-column']}),
                ll_to_earth(?, ?)
             ) * {$this->conversion} < ?
EOD;
    }

    /**
     * @return string
     */
    private function getOrderByDistanceSQL(string $orderDirection): string
    {
        return <<<EOD
            earth_distance(
                ll_to_earth({$this->config['lat-column']}, {$this->config['long-column']}),
                ll_to_earth(?, ?)
             ) * {$this->conversion} {$orderDirection}
EOD;
    }
}
