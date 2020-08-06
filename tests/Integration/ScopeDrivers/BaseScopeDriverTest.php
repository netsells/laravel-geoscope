<?php

namespace Netsells\GeoScope\Tests\Integration\ScopeDrivers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Netsells\GeoScope\Tests\Integration\Database\Models\Test;

abstract class BaseScopeDriverTest extends ScopeDriverTestCase
{
    use RefreshDatabase;

    protected $sqlSnippet;
    protected $scopeDriver;

    protected function assertEqualCollections(Collection $collection1, Collection $collection2)
    {
        $diff1 = $collection1->diff($collection2)->count();
        $diff2 = $collection2->diff($collection1)->count();

        // Check collection 1 contains no items that aren't in collection 2
        $this->assertEquals($diff1, 0, "collection 1 contained {$diff1} items that were not in collection 2");

        // Check collection 2 contains no items that are not in collection 1
        $this->assertEquals($diff2, 0, "collection 2 contained {$diff2} items that were not in collection 2");
    }

    protected function createNearbyModels(int $numberToCreate = 20, string $model = Test::class, array $options = []): iterable
    {
        $latlongs = collect($this->getLatLongs()->get('nearby_points'));

        return factory($model, $numberToCreate)->create($options)
            ->each(function ($testModel) use ($latlongs) {
                $testModel->latitude = $latlongs->random()['latitude'];
                $testModel->longitude = $latlongs->random()['longitude'];
                $testModel->save();
            });
    }

    protected function createFarAwayModels(int $numberToCreate = 20, string $model = Test::class, array $options = []): iterable
    {
        $latlongs = collect($this->getLatLongs()->get('far_away_points'));

        return factory($model, $numberToCreate)->create($options)
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
