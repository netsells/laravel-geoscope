<?php

namespace Netsells\GeoScope\Validators;

use Netsells\GeoScope\Exceptions\ScopeDriverNotFoundException;
use Netsells\GeoScope\ScopeDriverFactory;

class ScopeDriverFieldValidator
{
    /**
     * @param string $field
     * @throws ScopeDriverNotFoundException
     */
    public function validate(string $field): void
    {
        $validScopeDriverKeys = array_keys(app(ScopeDriverFactory::class)->getRegisteredScopeDrivers());

        if (!in_array($field, $validScopeDriverKeys)) {
            throw new ScopeDriverNotFoundException("No registered scope driver for {$field} connection");
        }
    }
}
