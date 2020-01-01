<?php

namespace Netsells\GeoScope\Tests\Integration\ScopeDrivers\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Netsells\GeoScope\Tests\Test;

trait ScopeDriverDatabaseTests
{
    /**
     * @test
     */
    public function within_distance_of_returns_correct_results()
    {
        $centralPoint = $this->getNearbyLatLongs()->get('central_point');

        $this->createNearbyModels();

        $actual = Test::withinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 20)->get();

        $this->assertEquals(20, $actual->count());
    }

    /**
     * @test
     */
    public function or_within_distance_of_returns_correct_results()
    {
        $centralPoint = $this->getNearbyLatLongs()->get('central_point');

        $invalidResult = factory(Test::class)->create([
            'latitude' => '55.1234',
            'longitude' => '-67.234'
        ]);

        $this->createNearbyModels();

        $actual = Test::orWithinDistanceOf($centralPoint['latitude'], $centralPoint['longitude'], 20)->get();

        $this->assertEquals(20, $actual->count());
    }

    protected function createNearbyModels(): void
    {
        $latlongs = collect($this->getNearbyLatLongs()->get('nearby_points'));

        factory(Test::class, 20)->create()
            ->each(function ($testModel) use ($latlongs) {
                $testModel->latitude = $latlongs->random()['latitude'];
                $testModel->longitude = $latlongs->random()['longitude'];
                $testModel->save();
            });
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

    protected function getNearbyLatLongs(): Collection
    {
        return collect([
            'central_point' =>
                [
                    'latitude' => 39.949242,
                    'longitude' => -76.743683
                ],
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
                        -76.743899,
                        'longitude' => -76.743899,
                    ],
                    [
                        'latitude' => 39.955460,
                        'longitude' => -76.745734,
                    ],
                ],
        ]);
    }
}
