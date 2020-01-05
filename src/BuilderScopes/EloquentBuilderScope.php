<?php

namespace Netsells\GeoScope\BuilderScopes;

use Illuminate\Database\Eloquent\Builder;
use Netsells\GeoScope\Config\Managers\EloquentBuilderConfigManager;

final class EloquentBuilderScope extends AbstractBuilderScope
{
    /**
     * EloquentBuilderScope constructor.
     * @param Builder $query
     * @param null $configOption
     * @throws \Netsells\GeoScope\Exceptions\InvalidConfigException
     */
    public function __construct(Builder $query, $configOption = null)
    {
        $this->query = $query;

        $this->config = app(EloquentBuilderConfigManager::class, [
            'query' => $this->query,
            'configOption' => $configOption,
        ])->getConfig();

        $this->table = $this->query->getModel()->getTable();

        parent::__construct();
    }
}
