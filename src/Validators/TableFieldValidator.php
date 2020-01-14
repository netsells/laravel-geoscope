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
    public function validate(string $table, $column): void
    {
        $columnName = $this->getColumnName($table, $column);

        $hasColumn = Schema::hasColumn($table, $columnName);

        if (!$hasColumn) {
            throw new InvalidConfigException("{$table} has no column named {$columnName}");
        }
    }

    /**
     * @param string $table
     * @param string $column
     * @return mixed|string
     */
    private function getColumnName(string $table, string $column)
    {
        $explodedColumn = explode('.', $column);

        if ($explodedColumn[0] == $table) {
            return $explodedColumn[1];
        }

        return $column;
    }
}
