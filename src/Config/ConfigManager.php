<?php

namespace Netsells\GeoScope\Config;

use Illuminate\Database\Eloquent\Builder;

class ConfigManager
{
    protected $modelClass;
    protected $configOption;

    /**
     * ConfigManager constructor.
     * @param Builder $query
     * @param $configOption
     */
    public function __construct(Builder $query, $configOption = null)
    {
        $this->modelClass = get_class($query->getModel());
        $this->configOption = $configOption;

        $this->stateMachine = app(ConfigStateMachine::class, [
            'modelClass' => $this->modelClass,
            'configOption' => $configOption,
        ]);
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        if ($this->stateMachine->wantsBasicModelConfig()) {
            return $this->getSimpleModelConfig();
        }

        if ($this->stateMachine->wantsNestedModelConfig()) {
            return $this->getNestedModelConfig();
        }

        if ($this->stateMachine->wantsCustomConfig()) {
            return $this->getCustomConfig();
        }

        return config('geoscope.defaults');
    }

    /**
     * @return array
     */
    protected function getSimpleModelConfig(): array
    {
        return $this->getValidConfig(config("geoscope.models.{$this->modelClass}"));
    }

    /**
     * @return array
     */
    protected function getNestedModelConfig(): array
    {
        return $this->getValidConfig(config("geoscope.models.{$this->modelClass}.{$this->configOption}"));
    }

    /**
     * @return array
     */
    protected function getCustomConfig(): array
    {
        return $this->getValidConfig($this->configOption);
    }

    /**
     * @param array $inputConfig
     * @return array
     */
    protected function getValidConfig(array $inputConfig): array
    {
        $validConfig = config('geoscope.defaults');
        foreach ($validConfig as $key => $value) {
            if (array_key_exists($key, $inputConfig)) {
                $validConfig[$key] = $inputConfig[$key];
            }
        }

        return $validConfig;
    }

}
