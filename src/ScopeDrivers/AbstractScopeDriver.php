<?php

namespace Netsells\GeoScope\ScopeDrivers;

use Netsells\GeoScope\Interfaces\ScopeDriverInterface;

abstract class AbstractScopeDriver implements ScopeDriverInterface
{
    protected $config;
    protected $query;
    protected $conversion;

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config): AbstractScopeDriver
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @param float $conversion
     * @return $this
     */
    public function setConversion(float $conversion): AbstractScopeDriver
    {
        $this->conversion = $conversion;
        return $this;
    }

    /**
     * @return $this
     */
    public function setQuery($query): AbstractScopeDriver
    {
        $this->query = $query;
        return $this;
    }
}
