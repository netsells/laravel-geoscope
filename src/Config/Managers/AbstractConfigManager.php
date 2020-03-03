<?php

namespace Netsells\GeoScope\Config\Managers;

use Netsells\GeoScope\Config\ConfigSanitizer;
use Netsells\GeoScope\Interfaces\ConfigManagerInterface;

abstract class AbstractConfigManager implements ConfigManagerInterface
{
    const CONFIG_FIELD_LATITUDE_COLUMN = 'lat-column';
    const CONFIG_FIELD_LONGITUDE_COLUMN = 'long-column';
    const CONFIG_FIELD_DISTANCE_UNITS = 'units';
    const CONFIG_FIELD_SCOPE_DRIVER = 'scope-driver';
    const CONFIG_FIELD_WHITELISTED_DISTANCE_FIELD_NAMES = 'whitelisted-distance-from-field-names';

    const OPTIONAL_CONFIG_FIELDS = [
        'scope-driver'
    ];

    protected $configOption;
    protected $table;

    public function __construct(string $table)
    {
        $this->table = $table;
        $this->config = config('geoscope.defaults');
        $this->config[self::CONFIG_FIELD_WHITELISTED_DISTANCE_FIELD_NAMES] = config('geoscope.' . self::CONFIG_FIELD_WHITELISTED_DISTANCE_FIELD_NAMES);
    }

    /**
     * @param array $inputConfig
     * @return array
     */
    protected function getValidConfig(array $inputConfig): array
    {
        // Add all compulsory fields
        foreach ($this->config as $key => $value) {
            if (array_key_exists($key, $inputConfig)) {
                $this->config[$key] = $inputConfig[$key];
            }
        }

        // Add any optional fields that are present
        foreach (self::OPTIONAL_CONFIG_FIELDS as $optionalField) {
            if (array_key_exists($optionalField, $inputConfig)) {
                $this->config[$optionalField] = $inputConfig[$optionalField];
            }
        }

        return app(ConfigSanitizer::class)->getSanitizedConfig($this->config, $this->table);
    }

    /**
     * @return array
     */
    protected function getCustomConfig(): array
    {
        return $this->getValidConfig($this->configOption);
    }

    /**
     * @return array
     */
    protected function getDefaultConfig(): array
    {
        return app(ConfigSanitizer::class)->getSanitizedConfig($this->config, $this->table);
    }
}
