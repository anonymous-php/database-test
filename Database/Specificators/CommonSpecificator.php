<?php
declare(strict_types=1);

namespace FpDbTest\Database\Specificators;


class CommonSpecificator extends AbstractSpecificator
{

    protected function resolveInternal(mixed $value): mixed
    {
        return $this->resolveValue($value);
    }

}