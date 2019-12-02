# Laravel GeoScope

GeoScope is a laravel package that allows you to easily add distance based query restrictions to your
models.

## Installation

using composer:

```
$ composer require netsells/laravel-geo-scope
```

Then publish the config file using the following artisan command:
```
php artisan vendor:publish --provider Netsells\GeoScope\GeoScopeServiceProvider
```

## Usage

### Basic Usage

GeoScope includes the `Netsells\GeoScope\Traits\GeoScopeTrait` that can be added your models. The trait contains two scopes,
`withinDistanceOf` and `orWithinDistanceOf`. `withinDistanceOf` will add a `where` where clause to your query and `orWithinDistanceOf` 
will add an `orWhere`. Both of these methods accept 3 parameters, a latitude, longitude and distance.

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Netsells\GeoScope\Traits\GeoScopeTrait;

class Job extends Model
{
    use GeoScopeTrait;
    //
}
```
the scopes can then be applied to any model queries:

```php

// Gets all jobs within 20 miles of the given latitude and longitude
$jobs = Job::withinDistanceOf(53.957962, -1.085485, 20)->get();

// Gets all jobs within 20 miles of the first lat and long or within 20 miles
// of the second lat long 
$jobs = Job::withinDistanceOf(53.957962, -1.085485, 20)
            ->orWithinDistanceOf(52.143542, -2.08556, 20);
```

### Configuration

When adding the `GeoScopeTrait` to your models you can define the latitude, longitude and distance units to be used by
the trait in the geoscope.php config file.

```php
 'models' => [
        App\Job::class => [
           'lat-column' => 'custom-lat-column-name',
           'long-column' => 'custom-long-column-name',
           'units' => 'meters'
        ]
    ]
```

Should you wish to use the scope for multiple latitude and longitude columns on the same model you can so so by creating
multiple configurations within the same model key.

```php
    'models' => [
        App\Job::class => [
            'location1' => [
                'lat-column' => 'custom-lat-column-name',
                'long-column' => 'custom-long-column-name',
                'units' => 'meters'
            ],
            'location2' => [
                'lat-column' => 'custom-lat-column-name',
                'long-column' => 'custom-long-column-name',
                'units' => 'meters' 
            ]
        ]
    ]
```
The key for the model config you wish to use can then be passed as a fourth parameter to
both the `withinDistanceOf` and `orWithinDistanceOf` scopes.

```php

$jobs = Job::withinDistanceOf(53.957962, -1.085485, 20, 'location1')->get();

$jobs2 = Job::withinDistanceOf(53.957962, -1.085485, 20, 'location1')
            ->orWithinDistanceOf(52.143542, -2.08556, 20, 'location2');
```
You may also pass in an array of config items as the third parameter to both the `withinDistanceOf` and `orWithinDistanceOf` scopes.
```php

$jobs = Job::withinDistanceOf(53.957962, -1.085485, 20, [
                'lat-column' => 'lat-column-1',
                'long-column' => 'long-column-1',
                'units' => 'meters'
            ])->get();

$jobs2 = Job::withinDistanceOf(53.957962, -1.085485, 20, 'location1')
            ->orWithinDistanceOf(52.143542, -2.08556, 20, [
                'units' => 'meters'
            ])->get();
```
**Any missing config cptions will be replaced with the defaults defined in `config('geoscope.defaults')`. 
Passing invalid config keys will also cause GeoScope to fallback to these defaults.**
