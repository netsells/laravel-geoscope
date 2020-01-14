<?php

namespace Netsells\GeoScope\Config\Managers;

use Illuminate\Database\Eloquent\Builder;
use Netsells\GeoScope\Config\ConfigStateMachine;

class EloquentBuilderConfigManager extends AbstractConfigManager
{
    protected $modelClass;
    protected $stateMachine;

    /**
     * EloquentBuilderConfigManager constructor.
     * @param Builder $query
     * @param $configOption
     */
    public function __construct(Builder $query, string $table, $configOption = null)
    {
        $this->modelClass = get_class($query->getModel());
        $this->configOption = $configOption;

        $this->stateMachine = app(ConfigStateMachine::class, [
            'configOption' => $configOption,
            'modelClass' => $this->modelClass,
        ]);

        parent::__construct($table);
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

        return $this->getDefaultConfig();
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
}
