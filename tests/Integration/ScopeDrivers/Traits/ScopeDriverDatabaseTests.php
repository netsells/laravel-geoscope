<?php

namespace Netsells\GeoScope\Tests\Integration\ScopeDrivers\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Netsells\GeoScope\Exceptions\InvalidConfigException;
use Netsells\GeoScope\Tests\Test;

trait ScopeDriverDatabaseTests
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
    public function within_distance_of_returns_query_correct_builder()
    {
        $actual = $this->scopeDriver->withinDistanceOf(54.7742, -1.33919, 20);

        $this->assertSame($actual, $this->query);
        $this->assertEquals(get_class($actual), Builder::class);
    }

    /**
     * @test
     */
    public function or_within_distance_of_returns_query_correct_builder()
    {
        $actual = $this->scopeDriver->withinDistanceOf(54.7742, -1.33919, 20);

        $this->assertSame($actual, $this->query);
        $this->assertEquals(get_class($actual), Builder::class);
    }

    /**
     * @test
     */
    public function within_distance_of_applied_correct_sql()
    {
        $lat = 54.60653;
        $long = -3.347538;
        $dist = 20;

        $query = $this->scopeDriver->withinDistanceOf($lat, $long, $dist);
        $bindings = $this->query->getBindings();

        $this->assertStringContainsString($this->sqlSnippet, $query->toSql());
        $this->assertStringContainsString(' where ', $query->toSql());

        $this->assertCount(3, $bindings);

        $this->assertTrue(in_array($lat, $bindings));
        $this->assertTrue(in_array($long, $bindings));
        $this->assertTrue(in_array($dist, $bindings));
    }

    /**
     * @test
     */
    public function or_within_distance_of_applied_correct_sql()
    {
        $lat = 54.60653;
        $long = -3.347538;
        $dist = 20;

        $this->query->where('id', 1);
        $query = $this->scopeDriver->orWithinDistanceOf($lat, $long, $dist);

        $bindings = $this->query->getBindings();

        $this->assertStringContainsString($this->sqlSnippet, $query->toSql());
        $this->assertStringContainsString(' or ', $query->toSql());

        $this->assertCount(4, $bindings);

        $this->assertTrue(in_array($lat, $bindings));
        $this->assertTrue(in_array($long, $bindings));
        $this->assertTrue(in_array($dist, $bindings));
        $this->assertTrue(in_array(1, $bindings));
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

    protected function assertEqualCollections(Collection $collection1, Collection $collection2)
    {
        $diff1 = $collection1->diff($collection2)->count();
        $diff2 = $collection2->diff($collection1)->count();

        // Check collection 1 contains no items that aren't in collection 2
        $this->assertEquals($diff1, 0, "collection 1 contained {$diff1} items that were not in collection 2");

        // Check collection 2 contains no items that are not in collection 1
        $this->assertEquals($diff2, 0, "collection 2 contained {$diff2} items that were not in collection 2");
    }

    protected function createNearbyModels(int $numberToCreate = 20): iterable
    {
        $latlongs = collect($this->getLatLongs()->get('nearby_points'));

        return factory(Test::class, $numberToCreate)->create()
            ->each(function ($testModel) use ($latlongs) {
                $testModel->latitude = $latlongs->random()['latitude'];
                $testModel->longitude = $latlongs->random()['longitude'];
                $testModel->save();
            });
    }

    protected function createFarAwayModels(int $numberToCreate = 20): iterable
    {
        $latlongs = collect($this->getLatLongs()->get('far_away_points'));

        return factory(Test::class, $numberToCreate)->create()
            ->each(function ($testModel) use ($latlongs) {
                $testModel->latitude = $latlongs->random()['latitude'];
                $testModel->longitude = $latlongs->random()['longitude'];
                $testModel->save();
            });
    }

    protected function getLatLongs(): Collection
    {
        return collect([
            'central_point' =>
                [
                    'latitude' => 39.949242,
                    'longitude' => -76.743683
                ],
            // All of these points are within 10 miles of the central point
            'nearby_points' =>
                [
                    [
                        'latitude' => 39.950858,
                        'longitude' => -76.741010,
                    ],
                    [
                        'latitude' => 39.945988,
                        'longitude' => -76.747788,
                    ],
                    [
                        'latitude' => 39.953489,
                        'longitude' => -76.736120,
                    ],
                    [
                        'latitude' => 39.953936,
                        'longitude' => -76.738777,
                    ],
                    [
                        'latitude' => 39.953288,
                        'longitude' => -76.742254,
                    ],
                    [
                        'latitude' => 39.955550,
                        'longitude' => -76.753351,
                    ],
                    [
                        'latitude' => 39.954777,
                        'longitude' => -76.747418,
                    ],
                    [
                        'latitude' => 39.955460,
                        'longitude' => -76.743899,
                    ],
                    [
                        'latitude' => 39.955460,
                        'longitude' => -76.745734,
                    ],
                ],
            // All of these points are around 530 miles away from the central point
            // And all are less than 540 miles away
            'far_away_points' =>
                [
                    [
                        'latitude' => 40.950858,
                        'longitude' => -86.741010,
                    ],
                    [
                        'latitude' => 40.945988,
                        'longitude' => -86.747788,
                    ],
                    [
                        'latitude' => 40.953489,
                        'longitude' => -86.736120,
                    ],
                    [
                        'latitude' => 40.953936,
                        'longitude' => -86.738777,
                    ],
                    [
                        'latitude' => 40.953288,
                        'longitude' => -86.742254,
                    ],
                    [
                        'latitude' => 40.955550,
                        'longitude' => -86.753351,
                    ],
                    [
                        'latitude' => 40.954777,
                        'longitude' => -86.747418,
                    ],
                    [
                        'latitude' => 40.955460,
                        'longitude' => -86.743899,
                    ],
                    [
                        'latitude' => 40.955460,
                        'longitude' => -86.745734,
                    ],
                ]
        ]);
    }
}
