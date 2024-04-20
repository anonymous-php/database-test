<?php
declare(strict_types=1);

namespace FpDbTest\Database\Specificators;


class FloatSpecificator extends AbstractSpecificator implements SpecificatorInterface
{

    protected function resolveInternal(mixed $value): float
    {
        return $this->resolveFloat($value);
    }

}