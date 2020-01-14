<?php

namespace Netsells\GeoScope\Config\Managers;

use Illuminate\Database\Query\Builder;
use Netsells\GeoScope\Config\ConfigStateMachine;

class DatabaseBuilderConfigManager extends AbstractConfigManager
{
    /**
     * EloquentBuilderConfigManager constructor.
     * @param $configOption
     */
    public function __construct(Builder $query, $table, $configOption = null)
    {
        $this->query = $query;
        $this->configOption = $configOption;

        parent::__construct($table);
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        $stateMachine = app(ConfigStateMachine::class, [
            'configOption' => $this->configOption,
        ]);

        if ($stateMachine->wantsCustomConfig()) {
            return $this->getCustomConfig();
        }

        return $this->getDefaultConfig();
    }
}
