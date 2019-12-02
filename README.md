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

GeoScope includes the `Netsells\GeoScope\Traits\GeoScopeTrait` that can be added to your models. The trait contains two scopes,
`withinDistanceOf` and `orWithinDistanceOf`. `withinDistanceOf` will add a `where` where clause to your query and `orWithinDistanceOf` 
will add an `orWhere`. Both of these methods accept 3 parameters, a latitude, longitude and distance. Both the latitude 
and longitude should be given in degrees.

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
the scopes can then be applied to any model query:

```php

// Gets all jobs within 20 miles of the given latitude and longitude
$jobs = Job::withinDistanceOf(53.957962, -1.085485, 20)->get();

// Gets all jobs within 20 miles of the first lat and long or within 20 miles
// of the second lat long 
$jobs = Job::withinDistanceOf(53.957962, -1.085485, 20)
            ->orWithinDistanceOf(52.143542, -2.08556, 20);
```

### Configuration

When adding the `GeoScopeTrait` to your model you can define the latitude, longitude and distance units to be used by
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

Should you wish to use the scope for multiple latitude and longitude columns on the same model you can so by creating
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
You may also pass in an array of config items as the fourth parameter to both the `withinDistanceOf` and `orWithinDistanceOf` scopes.
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
Any missing config options will be replaced with the defaults defined in `config('geoscope.defaults')`. 
**Passing invalid config keys will also cause GeoScope to fallback to these defaults for all config fields.**

### Scope Drivers
Under the hood, GeoScope uses different drivers to ensure that the distance queries are optimised to the database connection 
being used. Scope drivers correspond to the database drivers used by Laravel. GeoScope will automatically detect the database driver being used 
by Laravel and choose the correct scope driver for it. Out of the box GeoScope includes two scope drivers, one for MySQL 
which uses the built in `ST_Distance_Sphere()` function, and a default driver which uses an SQL based haversine calculation.
GeoScope will fall back to the default driver is no database specific driver is registered.

#### Creating Custom Scope Drivers
GeoScope allows you to define and register custom scope drivers. To create a custom scope driver create a class that extends
`Netsells\GeoScope\ScopeDrivers\AbstractScopeDriver` . The new driver must then implement the methods outlined in 
`Netsells\GeoScope\Interfaces\ScopeDriverInterface` (below).

```php
<?php


namespace Netsells\GeoScope\Interfaces;


use Illuminate\Database\Eloquent\Builder;

interface ScopeDriverInterface
{
    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     * @return Builder
     */
    public function withinDistanceOf(float $lat, float $long, float $distance): Builder;

    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     * @return Builder
     */
    public function orWithinDistanceOf(float $lat, float $long, float $distance): Builder;
}
``` 
The Eloquent Query Builder instance is available within your driver via the `$this->query` property.

#### Registering Custom Scope Drivers
Custom scope drivers can be registered using the `registerDriverStrategy` method on the `ScopeDriverFactory` class. 
Registration should normally be done within the `register` method of a service provider.

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Netsells\GeoScope\ScopeDriverFactory;
use App\Services\GeoScope\ScopeDrivers\PostgreSQLScopeDriver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app(ScopeDriverFactory::class)->registerDriverStrategy('pgsql', PostgreSQLScopeDriver::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
```

**If you create a custom scope driver, please consider putting in a pull Request to add it to the package so it may be used by others.**
