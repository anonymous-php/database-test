<?php
declare(strict_types=1);

namespace FpDbTest\Database\Specificators;


class IntegerSpecificator extends AbstractSpecificator
{

    protected function resolveInternal(mixed $value): int
    {
        return $this->resolveInt($value);
    }

}