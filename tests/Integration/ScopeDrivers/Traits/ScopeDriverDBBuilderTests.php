<?php

namespace Netsells\GeoScope\Tests\Integration\ScopeDrivers\Traits;

use Netsells\GeoScope\Tests\Test;
use Illuminate\Support\Facades\DB;
use Netsells\GeoScope\Tests\Test2;
use Netsells\GeoScope\Exceptions\InvalidDistanceFieldNameException;

trait ScopeDriverDBBuilderTests
{
    /**
     * @test
     */
    public function builder_macro_returns_correct_results_for_within_distance_of()
    {
        $centralPoint = $this->getLatLongs()->get('central_point');

        factory(Test::class, 30)->create();
        $expected = $this->createNearbyModels(20);

        $actual = DB::table('tests')->withinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 1)->get();

        $this->assertEquals(20, $actual->count());
        $this->assertEquals($expected[0]->id, $actual[0]->id);
        $this->assertEquals($expected[12]->id, $actual[12]->id);
        $this->assertEquals($expected[19]->id, $actual[19]->id);
    }

    /**
     * @test
     */
    public function builder_macro_returns_correct_results_for_or_within_distance_of()
    {
        $centralPoint = $this->getLatLongs()->get('central_point');
        $location2Point = $this->getLatLongs()->get('far_away_points')[0];

        factory(Test::class, 30)->create();
        $location1Results = $this->createNearbyModels(18);
        $location2Results = $this->createFarAwayModels(17);

        $actual = DB::table('tests')->withinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 1)
            ->orWithinDistanceOf($location2Point['latitude'], $location2Point['longitude'], 1)
            ->get();

        $expected = $location1Results->merge($location2Results);

        $this->assertEquals($expected->count(), $actual->count());
        $this->assertEquals($expected[0]->id, $actual[0]->id);
        $this->assertEquals($expected[12]->id, $actual[12]->id);
        $this->assertEquals($expected[19]->id, $actual[19]->id);
        $this->assertEquals($expected[34]->id, $actual[34]->id);
    }

    /**
     * @test
     */
    public function builder_macro_order_by_distance_from_returns_correct_results()
    {
        $centralPoint = $this->getLatLongs()->get('central_point');

        factory(Test::class, 30)->create();

        $results1 = DB::table('tests')->orderByDistanceFrom($centralPoint['latitude'], $centralPoint['longitude'], 'asc')->get();
        $results2 = DB::table('tests')->orderByDistanceFrom($centralPoint['latitude'], $centralPoint['longitude'], 'desc')->get();

        $this->assertEquals($results1->pluck('id'), $results2->reverse()->pluck('id'));
    }
}
