<?php

namespace Netsells\GeoScope\Config;

use Netsells\GeoScope\BuilderScopes\AbstractBuilderScope;
use Netsells\GeoScope\Config\Managers\AbstractConfigManager;
use Netsells\GeoScope\Exceptions\InvalidConfigException;
use Netsells\GeoScope\Exceptions\ScopeDriverNotFoundException;
use Netsells\GeoScope\Validators\ScopeDriverFieldValidator;
use Netsells\GeoScope\Validators\TableFieldValidator;
use Netsells\GeoScope\Validators\UnitsFieldValidator;

class ConfigSanitizer
{
    private const VALID_CONFIG_FIELDS = [
        AbstractConfigManager::CONFIG_FIELD_LATITUDE_COLUMN,
        AbstractConfigManager::CONFIG_FIELD_LONGITUDE_COLUMN,
        AbstractConfigManager::CONFIG_FIELD_DISTANCE_UNITS,
        AbstractConfigManager::CONFIG_FIELD_SCOPE_DRIVER,
        AbstractConfigManager::CONFIG_FIELD_WHITELISTED_DISTANCE_FIELD_NAMES,
    ];

    /**
     * @param array $config
     * @throws InvalidConfigException
     * @throws ScopeDriverNotFoundException
     */
    public function getSanitizedConfig(array $config, string $table)
    {
        $this->validateLatLongFields($config, $table);
        $this->validateDistanceUnitsField($config);
        $this->validateScopeDriverField($config);

        foreach ($config as $configField => $configItem) {
            if (!in_array($configField, self::VALID_CONFIG_FIELDS)) {
                unset($config[$configField]);
            }
        }

        $sanitizedConfig = $this->convertTableColumnsToFullyQualified($config, $table);

        return $sanitizedConfig;
    }

    /**
     * @throws InvalidConfigException
     */
    private function validateLatLongFields(array $config, string $table)
    {
        $tableFieldValidator = app(TableFieldValidator::class);

        $tableFieldValidator->validate($table, $config['lat-column']);
        $tableFieldValidator->validate($table, $config['long-column']);
    }

    /**
     * @throws InvalidConfigException
     */
    private function validateDistanceUnitsField(array $config)
    {
        if (!array_key_exists(AbstractConfigManager::CONFIG_FIELD_DISTANCE_UNITS, $config)) {
            return $config[AbstractConfigManager::CONFIG_FIELD_DISTANCE_UNITS] = AbstractBuilderScope::DISTANCE_UNITS_MILES;
        }

        app(UnitsFieldValidator::class)
            ->validate($config[AbstractConfigManager::CONFIG_FIELD_DISTANCE_UNITS]);
    }

    /**
     * @throws ScopeDriverNotFoundException
     */
    private function validateScopeDriverField(array $config)
    {
        if (!in_array(AbstractConfigManager::CONFIG_FIELD_SCOPE_DRIVER, $config)) {
            return;
        }

        app(ScopeDriverFieldValidator::class)
            ->validate($config[AbstractConfigManager::CONFIG_FIELD_SCOPE_DRIVER]);
    }

    /**
     * @param array $config
     * @param string $table
     * @return array
     * @throws InvalidConfigException
     */
    private function convertTableColumnsToFullyQualified(array $config, string $table): array
    {
        $latCol = $config[AbstractConfigManager::CONFIG_FIELD_LATITUDE_COLUMN];
        $longCol = $config[AbstractConfigManager::CONFIG_FIELD_LONGITUDE_COLUMN];

        $config["lat-column"] = $this->getFullyQualifiedTableColumn($latCol, $table);
        $config["long-column"] = $this->getFullyQualifiedTableColumn($longCol, $table);

        return $config;
    }

    /**
     * @param string $column
     * @param string $table
     * @return string
     * @throws InvalidConfigException
     */
    private function getFullyQualifiedTableColumn(string $column, string $table): string
    {
        $explodedCol = explode('.', $column);

        if ($explodedCol[0] == $table) {
            return $column;
        }

        if (count($explodedCol) == 1) {
            return $table . '.' . $column;
        }

        throw new InvalidConfigException("Could not get fully qualified table column for config value {$column}");
    }
}
