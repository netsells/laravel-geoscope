<?php

namespace Netsells\GeoScope\ScopeDrivers;

use Netsells\GeoScope\Interfaces\ScopeDriverInterface;
use Netsells\GeoScope\Config\Managers\AbstractConfigManager;
use Netsells\GeoScope\Exceptions\InvalidOrderDirectionParameter;
use Netsells\GeoScope\Exceptions\InvalidDistanceFieldNameException;

abstract class AbstractScopeDriver implements ScopeDriverInterface
{
    protected const ALLOWED_ORDER_DIRECTION_IDENTIFIERS = ['asc', 'desc'];

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

    /**
     * @throws InvalidOrderDirectionParameter
     * @return void
     */
    protected function checkOrderDirectionIdentifier(string $orderDirection): void
    {
        if (!in_array($orderDirection, self::ALLOWED_ORDER_DIRECTION_IDENTIFIERS)) {
            throw new InvalidOrderDirectionParameter("{$orderDirection} is not a valid order direction");
        }
    }

    /**
     * @throws InvalidDistanceFieldNameException
     * @return string
     */
    protected function getValidFieldName(?string $fieldName): string
    {
        if (!$fieldName) {
            $fieldName = 'distance';
        }
        
        $validFieldNames = $this->config[AbstractConfigManager::CONFIG_FIELD_WHITELISTED_DISTANCE_FIELD_NAMES];

        if (is_array($validFieldNames) && !in_array($fieldName, $validFieldNames)) {
            $class = self::class;

            throw new InvalidDistanceFieldNameException(
                "The field name passed to {$class}::addDistanceFromField() has not been registered in the geoscope.php config file"
            );
        }

        return $fieldName;
    }
}
