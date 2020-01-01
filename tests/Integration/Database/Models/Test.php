<?php

namespace Netsells\GeoScope\Tests;

use Illuminate\Database\Eloquent\Model;
use Netsells\GeoScope\Traits\GeoScopeTrait;

class Test extends Model
{
    use GeoScopeTrait;
}
