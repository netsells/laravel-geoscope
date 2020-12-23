<?php

namespace Netsells\GeoScope\ScopeDrivers;

use Illuminate\Support\Facades\DB;

final class MariaDbScopeDriver extends AbstractScopeDriver
{
    public function __construct()
    {
        $sql =  <<<EOD
            CREATE FUNCTION IF NOT EXISTS ST_Distance_Sphere(pt1 POINT, pt2 POINT)
            RETURNS double(10,2)
            
            RETURN 6371000 * 2 * ASIN(
                SQRT(
                    POWER(SIN((ST_Y(pt2) - ST_Y(pt1)) * pi()/180 / 2), 2) +
                    COS(ST_Y(pt1) * pi()/180 ) *
                    COS(ST_Y(pt2) * pi()/180) *
                    POWER(SIN((ST_X(pt2) - ST_X(pt1)) * pi()/180 / 2), 2)
                )
            );
EOD;
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     * @return mixed
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
     * @return mixed
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
     * @return mixed
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
     * @return mixed
     */
    public function addDistanceFromField(float $lat, float $long, string $fieldName = null)
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
