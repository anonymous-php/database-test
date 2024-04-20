<?php
declare(strict_types=1);

namespace FpDbTest\Database\Specificators;


class ArraySpecificator extends AbstractSpecificator
{

    protected function resolveInternal(mixed $value): mixed
    {
        if (!is_array($value)) {
            throw new BuilderSpecificatorException($this, 'Array must be provided');
        }

        return array_is_list($value)
            ? $this->resolveList($value)
            : $this->resolveAssoc($value);
    }

    private function resolveList(array $array): string
    {
        return implode(', ', array_map(function (mixed $value) {
            return $this->resolveValue($value);
        }, $array));
    }

    private function resolveAssoc(array $array): string
    {
        foreach ($array as $key => $value) {
            $list[] = sprintf(
                '%s = %s',
                $this->resolveIdentifier($key),
                $this->resolveValue($value),
            );
        }

        return implode(', ', $list);
    }

}