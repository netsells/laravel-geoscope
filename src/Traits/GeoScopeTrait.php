<?php

namespace Netsells\GeoScope\Traits;

use Illuminate\Database\Eloquent\Builder;
use Netsells\GeoScope\Exceptions\InvalidConfigException;
use Netsells\GeoScope\BuilderScopes\EloquentBuilderScope;
use Netsells\GeoScope\Exceptions\InvalidDistanceFieldNameException;

trait GeoScopeTrait
{
    /**
     * @param Builder $query
     * @param float $lat
     * @param float $long
     * @param float $distance
     * @param null $configOption
     * @return mixed
     * @throws InvalidConfigException
     */
    public function scopeWithinDistanceOf(
        Builder $query,
        float $lat,
        float $long,
        float $distance,
        $configOption = null
    ) {
        return app(EloquentBuilderScope::class, [
            'query' => $query,
            'configOption' => $configOption,
        ])->withinDistanceOf($lat, $long, $distance);
    }

    /**
     * @param Builder $query
     * @param float $lat
     * @param float $long
     * @param float $distance
     * @param null $configOption
     * @return mixed
     * @throws InvalidConfigException
     */
    public function scopeOrWithinDistanceOf(
        Builder $query,
        float $lat,
        float $long,
        float $distance,
        $configOption = null
    ) {
        return app(EloquentBuilderScope::class, [
            'query' => $query,
            'configOption' => $configOption,
        ])->orWithinDistanceOf($lat, $long, $distance);
    }

    /**
     * @throws InvalidOrderDirectionParameter
     * @param Builder $query
     * @param float $lat
     * @param float $long
     * @param float $orderDirection
     * @return mixed
     */
    public function scopeOrderByDistanceFrom(
        Builder $query,
        float $lat,
        float $long,
        string $orderDirection = 'asc'
    ) {
        return app(EloquentBuilderScope::class, [
            'query' => $query,
        ])->orderByDistanceFrom($lat, $long, $orderDirection);
    }

    /**
     * @throws InvalidDistanceFieldNameException
     * @param Builder $query
     * @param float $lat
     * @param float $long
     * @return mixed
     */
    public function scopeAddDistanceFromField(
        Builder $query,
        float $lat,
        float $long,
        string $fieldName = null
    ) {
        return app(EloquentBuilderScope::class, [
            'query' => $query,
        ])->addDistanceFromField($lat, $long, $fieldName);
    }
}
