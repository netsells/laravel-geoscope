<?php

return [

    /*
     * default column names that GeoScope will use
     * if none are stated for a model
     */
    'defaults' => [
        'lat-column' => 'latitude',
        'long-column' => 'longitude',
        'units' => 'miles', // miles, kilometers or meters,
    ],

    /*
     * Register model specific settings here
     */
//    'models' => [
//        SomeModel::class => [
//            'lat-column' => 'custom-lat-column-name',
//            'long-column' => 'custom-long-column-name',
//            'units' => 'meters',
//        ]
//    ],

];
