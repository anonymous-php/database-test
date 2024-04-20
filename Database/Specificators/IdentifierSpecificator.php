<?php
declare(strict_types=1);

namespace FpDbTest\Database\Specificators;


class IdentifierSpecificator extends AbstractSpecificator
{

    protected function resolveInternal(mixed $value): mixed
    {
        return implode(', ', array_map(function (string $identifier) {
            return $this->resolveIdentifier($identifier);
        }, (array)$value));
    }

}