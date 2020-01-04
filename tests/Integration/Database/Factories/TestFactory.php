<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    \Netsells\GeoScope\Tests\Test::class,
    function (Faker $faker) {
        return [
            'latitude' => $faker->latitude,
            'longitude' => $faker->longitude,
        ];
    }
);
