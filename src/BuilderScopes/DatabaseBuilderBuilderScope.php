<?php

namespace Netsells\GeoScope\BuilderScopes;

use Illuminate\Database\Query\Builder;
use Netsells\GeoScope\Config\Managers\DatabaseBuilderConfigManager;

final class DatabaseBuilderBuilderScope extends AbstractBuilderScope
{
    /**
     * DatabaseBuilderBuilderScope constructor.
     * @param Builder $query
     * @param null $configOption
     * @throws \Netsells\GeoScope\Exceptions\InvalidConfigException
     */
    public function __construct(Builder $query, $configOption = null)
    {
        $this->query = $query;

        $this->config = app(DatabaseBuilderConfigManager::class, [
            'query' => $this->query,
            'configOption' => $configOption,
        ])->getConfig();

        $this->table = $this->query->from;

        parent::__construct();
    }
}
