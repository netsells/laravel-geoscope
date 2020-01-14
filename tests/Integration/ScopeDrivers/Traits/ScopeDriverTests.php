<?php

namespace Netsells\GeoScope\Tests\Integration\ScopeDrivers\Traits;

trait ScopeDriverTests
{
    use ScopeDriverSQLTests;
    use ScopeDriverEloquentBuilderTests;
    use ScopeDriverDBBuilderTests;
}
