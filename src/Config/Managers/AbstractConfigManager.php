<?php

namespace Netsells\GeoScope\Config\Managers;

use Netsells\GeoScope\Interfaces\ConfigManagerInterface;

abstract class AbstractConfigManager implements ConfigManagerInterface
{
    const OPTIONAL_CONFIG_FIELDS = [
        'scope-driver'
    ];

    protected $configOption;

    /**
     * @param array $inputConfig
     * @return array
     */
    protected function getValidConfig(array $inputConfig): array
    {
        $validConfig = config('geoscope.defaults');

        // Add all compulsory fields
        foreach ($validConfig as $key => $value) {
            if (array_key_exists($key, $inputConfig)) {
                $validConfig[$key] = $inputConfig[$key];
            }
        }

        // Add any optional fields that are present
        foreach (self::OPTIONAL_CONFIG_FIELDS as $optionalField) {
            if (array_key_exists($optionalField, $inputConfig)) {
                $validConfig[$optionalField] = $inputConfig[$optionalField];
            }
        }

        return $validConfig;
    }

    /**
     * @return array
     */
    protected function getCustomConfig(): array
    {
        return $this->getValidConfig($this->configOption);
    }
}
