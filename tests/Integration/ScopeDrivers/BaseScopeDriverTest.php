<?php

namespace Netsells\GeoScope\Tests\Integration\ScopeDrivers;

use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class BaseScopeDriverTest extends ScopeDriverTestCase
{
    use RefreshDatabase;

    protected $sqlSnippet;
    protected $scopeDriver;
}
