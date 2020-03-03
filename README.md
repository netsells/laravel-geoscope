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
php artisan vendor:publish --tag=geoscope
```

## Usage

### Basic Usage

GeoScope includes the `Netsells\GeoScope\Traits\GeoScopeTrait` that can be added to your models. The trait contains two scopes,
`withinDistanceOf` and `orWithinDistanceOf`. `withinDistanceOf` will add a `where` clause to your query and `orWithinDistanceOf` 
will add an `orWhere`. Both of these methods accept 3 parameters, a latitude, longitude and distance. Both the latitude 
and longitude should be given in degrees. GeoScope with then use these to query against the specified lat long fields on that model.

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

Should you wish to use the scope for multiple latitude and longitude columns on the same model you can do so by creating
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

GeoScope also includes an `orderByDistanceFrom()` method that allows you to sort results by their distance from a specified lat long.

```php
    // order by distance in ascending order
    $results =  Job::orderByDistanceFrom(30.1234, -71.2176, 'asc')->get();

    // order by distance in descending order
    $results =  Job::orderByDistanceFrom(30.1234, -71.2176, 'desc')->get();
```

### The `addDistanceFrom()` method

A field can be added to each returned result with the calculated distance from the given lat long using the `addDistanceFromField()` method.

```php
    $results =  Job::addDistanceFromField(30.1234, -71.2176)->get();
```

before `addDistanceFrom()` is applied

```json
{
    "id": 1,
    "email": "vita.frami@example.com",
    "latitude": 39.95,
    "longitude": -76.74,
    "created_at": "2020-02-29 19:13:08",
    "updated_at": "2020-02-29 19:13:08",
}
```

After `addDistanceFrom()` is applied
```json
{
    "id": 1,
    "email": "vita.frami@example.com",
    "latitude": 39.95,
    "longitude": -76.74,
    "created_at": "2020-02-29 19:13:08",
    "updated_at": "2020-02-29 19:13:08",
    "distance": 0.2,
    "dist_units": "miles"
}
```

A custom field name can be third parameter can be passed to the `addDistanceFrom()` method if the name has been registered in the `whitelisted-distance-from-field-names` array of the geoscope.php config file. The distance field will have a default name of `distance` and the units field will have a default name of `distance_units` **The `addDistanceFromField()` method is only available through the GeoScopeTrait. It is not available on the database builder**

```php
   'whitelisted-distance-from-field-names' => [
       'custom_field_name'
   ]
```

```php
    $results =  Job::addDistanceFromField(30.1234, -71.2176, 'custom_field_name')->get();
```

```json
{
    "id": 1,
    "email": "vita.frami@example.com",
    "latitude": 39.95,
    "longitude": -76.74,
    "created_at": "2020-02-29 19:13:08",
    "updated_at": "2020-02-29 19:13:08",
    "custom_field_name": 0.2,
    "custom_field_name_units": "miles"
}
```

## Database Query Builder
Geoscope also allows you to call the `withinDistanceOf()`, `orWithinDistanceOf()` and `orderByDistanceFrom()` methods directly off the DB query builder:

```php
    $results =  DB::table('users')
                    ->withinDistanceOf(30.1234, -71.2176, 20)
                    ->join('jobs', 'jobs.user_id', '=', 'users.id')
                    ->get();
```

if you wish to alter the config options then you may pass an array as the fourth parameter to the `withinDistanceOf()` 
and `orWithinDistanceOf()` methods:

```php
    $results =  DB::table('users')->withinDistanceOf(30.1234, -71.2176, 20, [
                         'lat-column' => 'lat-column-1',
                         'long-column' => 'long-column-1',
                         'units' => 'meters'
                    ])->get();
```
order by distance example:

```php
    $results =  DB::table('users')->orderByDistanceFrom(30.1234, -71.2176, 'asc')->get();
```


### Scope Drivers
Under the hood, GeoScope uses different drivers to ensure that the distance queries are optimised to the database connection 
being used. Scope drivers correspond to the database drivers used by Laravel. GeoScope will automatically detect the database driver being used 
by Laravel and choose the correct scope driver for it. Out of the box GeoScope includes a MySQL scope driver
which uses `ST_Distance_Sphere()` function, a PostgreSQL scope driver which uses `earth_distance` and a SQL Server driver which uses `STDistance`.

**NOTE: The PostgreSQL driver requires you to have the postgres `earthdistance` module installed which can be done by executing the following SQL**
```sql
create extension if not exists cube;
create extension if not exists earthdistance;
```

#### Creating Custom Scope Drivers
GeoScope allows you to define and register custom scope drivers. To create a custom scope driver create a class that extends
`Netsells\GeoScope\ScopeDrivers\AbstractScopeDriver` . The new driver must then implement the methods outlined in 
`Netsells\GeoScope\Interfaces\ScopeDriverInterface` (below).

```php
<?php

namespace Netsells\GeoScope\Interfaces;

interface ScopeDriverInterface
{
    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     * @return mixed
     * Should return query instance
     */
    public function withinDistanceOf(float $lat, float $long, float $distance);

    /**
     * @param float $lat
     * @param float $long
     * @param float $distance
     * @return mixed
     * Should return query instance
     */
    public function orWithinDistanceOf(float $lat, float $long, float $distance);

    /**
     * @param float $lat
     * @param float $long
     * @param string $orderDirection - asc or desc
     * @return mixed
     * Should return query instance
     */
    public function orderByDistanceFrom(float $lat, float $long, string $orderDirection = 'asc');
}
``` 
The Query Builder instance is available within your driver via the `$this->query` property.

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
You may set an optional `scope-driver` config key if you wish to force a specific scope driver to be used.

```php
 'models' => [
        App\Job::class => [
           'lat-column' => 'custom-lat-column-name',
           'long-column' => 'custom-long-column-name',
           'units' => 'meters',
           'scope-driver' => 'mysql'
        ]
    ]
```

**If you create a custom scope driver, please consider putting in a pull Request to add it to the package so it may be used by others.**

#### Scope Driver Security

Due to the nature of the queries being run by GeoScope, both the `whereRaw()` and `orWhereRaw` methods are used. The drivers included by default
protect against sql injection attacks (using prepared statements and by checking for valid lat long column config values). It is important that when creating
custom scope drivers, that you also take this into consideration for any user input that you pass directly to it.
