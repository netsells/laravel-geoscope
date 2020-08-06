<?php

use Faker\Generator as Faker;
use Netsells\GeoScope\Tests\Integration\Database\Models\Test;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    Test::class,
    function (Faker $faker) {
        return [
            'latitude' => $faker->latitude,
            'longitude' => $faker->longitude,
        ];
    }
);

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    Test2::class,
    function (Faker $faker) {
        return [
            'latitude' => $faker->latitude,
            'longitude' => $faker->longitude,
            'test_id' => function () {
                return factory(Test::class)->create()->id;
            }
        ];
    }
);
