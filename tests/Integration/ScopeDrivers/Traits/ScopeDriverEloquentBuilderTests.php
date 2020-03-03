<?php

namespace Netsells\GeoScope\Tests\Integration\ScopeDrivers\Traits;

use Netsells\GeoScope\Exceptions\InvalidConfigException;
use Netsells\GeoScope\Exceptions\InvalidDistanceFieldNameException;
use Netsells\GeoScope\Exceptions\ScopeDriverNotFoundException;
use Netsells\GeoScope\Tests\Test;

trait ScopeDriverEloquentBuilderTests
{
    /**
     * @test
     */
    public function within_distance_of_returns_correct_results()
    {
        $centralPoint = $this->getLatLongs()->get('central_point');

        factory(Test::class, 30)->create();
        $expected = $this->createNearbyModels(20);

        $actual = Test::withinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 1)->get();

        $this->assertEquals(20, $actual->count());
        $this->assertEqualCollections($expected, $actual);
    }

    /**
     * @test
     */
    public function or_within_distance_of_returns_correct_results()
    {
        $centralPoint = $this->getLatLongs()->get('central_point');
        $location2Point = $this->getLatLongs()->get('far_away_points')[0];

        factory(Test::class, 30)->create();
        $location1Results = $this->createNearbyModels(18);
        $location2Results = $this->createFarAwayModels(17);

        $actual = Test::WithinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 1)
            ->orWithinDistanceOf($location2Point['latitude'], $location2Point['longitude'], 1)
            ->get();

        $expected = $location1Results->merge($location2Results);

        $this->assertEquals(35, $actual->count());
        $this->assertEqualCollections($expected, $actual);
    }

    /**
     * @test
     */
    public function order_by_distance_from_returns_correct_results()
    {
        $centralPoint = $this->getLatLongs()->get('central_point');

        factory(Test::class, 30)->create();

        $results1 = Test::orderByDistanceFrom($centralPoint['latitude'], $centralPoint['longitude'], 'asc')->get();
        $results2 = Test::orderByDistanceFrom($centralPoint['latitude'], $centralPoint['longitude'], 'desc')->get();

        $this->assertEquals($results1->pluck('id'), $results2->reverse()->pluck('id'));
    }

    /**
     * @test
     */
    public function add_distance_from_field_is_applied_correctly()
    {
        $centralPoint = $this->getLatLongs()->get('central_point');

        factory(Test::class, 30)->create();

        $results = Test::addDistanceFromField($centralPoint['latitude'], $centralPoint['longitude'])->get();

        $distanceField = $results->random()->distance;
        $distanceUnits = $results->random()->distance_units;

        $this->assertTrue(is_numeric($distanceField));
        $this->assertEquals($distanceUnits, 'miles');
    }

    /**
     * @test
     */
    public function custom_config_items_can_be_set_for_within_distance_of()
    {
        $centralPoint = $this->getLatLongs()->get('central_point');

        factory(Test::class, 30)->create();

        $expected = $this->createNearbyModels(20);
        $actual1 = Test::withinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 10)->get();

        $this->assertEquals(20, $actual1->count());
        $this->assertEqualCollections($expected, $actual1);

        $actual2 = Test::withinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 1600, [
            'units' => 'meters'
        ])->get();

        $this->assertEquals(20, $actual2->count());
        $this->assertEqualCollections($expected, $actual2);
    }

    /**
     * @test
     */
    public function invalid_lat_column_name_for_within_distance_of_throws_an_exception()
    {
        $this->expectException(InvalidConfigException::class);

        $centralPoint = $this->getLatLongs()->get('central_point');

        factory(Test::class, 30)->create();

        Test::withinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 1600, [
            'lat-column' => 'invalid lat column'
        ])->get();
    }

    /**
     * @test
     */
    public function invalid_long_column_name_for_within_distance_of_throws_an_exception()
    {
        $this->expectException(InvalidConfigException::class);

        $centralPoint = $this->getLatLongs()->get('central_point');

        factory(Test::class, 30)->create();

        Test::withinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 1600, [
            'long-column' => 'invalid long column'
        ])->get();
    }

    /**
     * @test
     */
    public function invalid_lat_column_name_for_or_within_distance_of_throws_an_exception()
    {
        $this->expectException(InvalidConfigException::class);

        $centralPoint = $this->getLatLongs()->get('central_point');

        factory(Test::class, 30)->create();

        Test::orWithinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 1600, [
            'lat-column' => 'invalid lat column'
        ])->get();
    }

    /**
     * @test
     */
    public function invalid_long_column_name_for_or_within_distance_of_throws_an_exception()
    {
        $this->expectException(InvalidConfigException::class);

        $centralPoint = $this->getLatLongs()->get('central_point');

        factory(Test::class, 30)->create();

        Test::orWithinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 1600, [
            'long-column' => 'invalid long column'
        ])->get();
    }

    /**
     * @test
     */
    public function invalid_distance_units_key_for_within_distance_of_throws_an_exception()
    {
        $this->expectException(InvalidConfigException::class);

        $centralPoint = $this->getLatLongs()->get('central_point');

        factory(Test::class, 30)->create();

        Test::withinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 1600, [
            'units' => 'invalid units'
        ])->get();
    }

    /**
     * @test
     */
    public function invalid_distance_units_key_for_or_within_distance_of_throws_an_exception()
    {
        $this->expectException(InvalidConfigException::class);

        $centralPoint = $this->getLatLongs()->get('central_point');

        factory(Test::class, 30)->create();

        Test::orWithinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 1600, [
            'units' => 'invalid units'
        ])->get();
    }

    /**
     * @test
     */
    public function invalid_scope_driver_value_for_within_distance_of_throws_an_exception()
    {
        $this->expectException(ScopeDriverNotFoundException::class);

        $centralPoint = $this->getLatLongs()->get('central_point');

        factory(Test::class, 30)->create();

        Test::withinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 1600, [
            'scope-driver' => 'invalid scope driver'
        ])->get();
    }

    /**
     * @test
     */
    public function invalid_scope_driver_value_for_or_within_distance_of_throws_an_exception()
    {
        $this->expectException(ScopeDriverNotFoundException::class);

        $centralPoint = $this->getLatLongs()->get('central_point');

        factory(Test::class, 30)->create();

        Test::orWithinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 1600, [
            'scope-driver' => 'invalid scope driver'
        ])->get();
    }

    /**
     * @test
     */
    public function column_name_with_table_is_accepted()
    {
        $centralPoint = $this->getLatLongs()->get('central_point');

        factory(Test::class, 30)->create();
        $expected = $this->createNearbyModels(20);

        $actual = Test::withinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 1, [
            'lat-column' => 'tests.latitude',
            'long-column' => 'tests.longitude'
        ])->get();

        $this->assertEquals(20, $actual->count());
        $this->assertEqualCollections($expected, $actual);
    }
}
