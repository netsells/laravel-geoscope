<?php

namespace Netsells\GeoScope\Validators;

use Illuminate\Support\Facades\Schema;
use Netsells\GeoScope\Exceptions\InvalidConfigException;

class TableFieldValidator
{
    /**
     * @param string $table
     * @param $column
     * @throws InvalidConfigException
     */
    public static function validate(string $table, $column): void
    {
        $hasColumn = Schema::hasColumn($table, $column);

        if (!$hasColumn) {
            throw new InvalidConfigException("{$table} has no column named {$column}");
        }
    }
}
