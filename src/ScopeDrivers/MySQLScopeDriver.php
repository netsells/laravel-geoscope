<?php

namespace Netsells\GeoScope\ScopeDrivers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class MySQLScopeDriver extends AbstractScopeDriver
{
    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     */
    public function withinDistanceOf(float $lat, float $long, float $distance)
    {
        return $this->query->whereRaw($this->getWithinDistanceSQL(), [
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
        return $this->query->orWhereRaw($this->getWithinDistanceSQL(), [
            $long,
            $lat,
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

        return $this->query->orderByRaw(DB::raw($this->getOrderByDistanceSQL($orderDirection)), [
            $long,
            $lat,
        ]);
    }

    /**
     * @param float $lat
     * @param float $long
     * @param float $orderDirection
     */
    public function addDistanceFromField(float $lat, float $long, ?string $fieldName = null)
    {
        $fieldName = $this->getValidFieldName($fieldName);

        $this->query->select();

        return $this->query->selectRaw($this->getSelectDistanceSQL($fieldName), [
            $long,
            $lat,
        ])->selectRaw("'{$this->config['units']}' as {$fieldName}_units");
    }

    /**
     * @return string
     */
    private function getWithinDistanceSQL(): string
    {
        return <<<EOD
            ST_Distance_Sphere(
                    point({$this->config['long-column']}, {$this->config['lat-column']}),
                    point(?, ?)
                ) * {$this->conversion} < ?
EOD;
    }

    /**
     * @return string
     */
    private function getOrderByDistanceSQL(string $orderDirection): string
    {
        return <<<EOD
            ST_Distance_Sphere(
                    point({$this->config['long-column']}, {$this->config['lat-column']}),
                    point(?, ?)
                ) * {$this->conversion} {$orderDirection}
EOD;
    }

    /**
     * @return string
     */
    private function getSelectDistanceSQL(string $fieldName): string
    {
        return <<<EOD
            ROUND(ST_Distance_Sphere(
                    point({$this->config['long-column']}, {$this->config['lat-column']}),
                    point(?, ?)
                ) * {$this->conversion}, 2) as {$fieldName}
EOD;
    }
}
