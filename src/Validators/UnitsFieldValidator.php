<?php

namespace Netsells\GeoScope\Validators;

use Netsells\GeoScope\BuilderScopes\AbstractBuilderScope;
use Netsells\GeoScope\Exceptions\InvalidConfigException;

class UnitsFieldValidator
{
    /**
     * @param string $field
     * @throws InvalidConfigException
     */
    public function validate(string $field): void
    {
        $validUnitKeys = array_keys(AbstractBuilderScope::DISTANCE_CONVERSION_FROM_METERS);

        if (!in_array($field, $validUnitKeys)) {
            throw new InvalidConfigException("{$field} is not a valid distance unit key");
        }
    }
}
