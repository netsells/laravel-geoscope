<?php

namespace Netsells\GeoScope\Config;

class ConfigStateMachine
{
    const CONFIG_FIELD_LATITUDE_COLUMN = 'lat-column';
    const CONFIG_FIELD_LONGITUDE_COLUMN = 'long-column';
    const CONFIG_FIELD_DISTANCE_UNITS = 'units';

    const VALID_CONFIG_FIELDS = [
        self::CONFIG_FIELD_LATITUDE_COLUMN,
        self::CONFIG_FIELD_LONGITUDE_COLUMN,
        self::CONFIG_FIELD_DISTANCE_UNITS,
    ];

    protected $modelClass;
    protected $configOption;

    /**
     * ConfigStateMachine constructor.
     * @param $modelClass
     * @param $configOption
     */
    public function __construct($modelClass, $configOption)
    {
        $this->modelClass = $modelClass;
        $this->configOption = $configOption;
    }

    /**
     * @return bool
     */
    public function wantsBasicModelConfig(): bool
    {
        return $this->hasValidModelConfig() && !$this->configOption;
    }

    /**
     * @return bool
     */
    public function wantsNestedModelConfig(): bool
    {
        return is_string($this->configOption) && $this->hasValidNestedModelConfig($this->configOption);
    }

    /**
     * @return bool
     */
    public function wantsCustomConfig(): bool
    {
        return $this->hasValidCustomConfig();
    }

    /**
     * @return bool
     */
    protected function hasValidCustomConfig(): bool
    {
        return is_array($this->configOption) && $this->hasValidKeys($this->configOption);
    }

    /**
     * @return bool
     */
    protected function hasValidModelConfig(): bool
    {
        if (!config()->has("geoscope.models.{$this->modelClass}")) {
            return false;
        }

        $modelConfig = config("geoscope.models.{$this->modelClass}");

        return is_array($modelConfig) && $this->hasValidKeys($modelConfig);
    }

    /**
     * @param null $configKey
     * @return bool
     */
    protected function hasValidNestedModelConfig($configKey = null): bool
    {
        if (is_string($configKey)) {
            return config()->has("geoscope.models.{$this->modelClass}.{$this->configOption}")
                && is_array(config("geoscope.models.{$this->modelClass}.{$this->configOption}"))
                && $this->hasValidKeys(config("geoscope.models.{$this->modelClass}.{$this->configOption}"));
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function hasValidKeys(array $config): bool
    {
        $hasValidKeys = true;

        foreach ($config as $configField => $value) {
            if (!in_array($configField, self::VALID_CONFIG_FIELDS)) {
                $hasValidKeys = false;
            }
        }

        return $hasValidKeys;
    }
}
