<?php
declare(strict_types=1);

namespace FpDbTest\Database\Specificators;


use FpDbTest\Database\Block;
use mysqli;

abstract class AbstractSpecificator extends Block implements SpecificatorInterface
{

    protected ?mysqli $mysqli;


    abstract protected function resolveInternal(mixed $value): mixed;


    public function resolve(mixed $value, ?mysqli $mysqli = null): void
    {
        $this->mysqli = $mysqli;

        switch (true) {
            case Block::SKIP === $value:
                $this->setValue($value);
                break;

            case null === $value:
                $this->setValue($this->resolveNull());
                break;

            default:
                $this->setValue($this->resolveInternal($value));
        }

        $this->mysqli = null;
    }

    protected function resolveValue(mixed $value): mixed
    {
        switch (true) {
            case null === $value:
                return $this->resolveNull();

            case is_int($value):
            case is_bool($value):
                return $this->resolveInt($value);

            case is_float($value):
                return $this->resolveFloat($value);

            case is_string($value):
                return $this->resolveString($value);
        }

        throw new BuilderSpecificatorException($this, 'Incompatible value type');
    }

    protected function resolveNull(): string
    {
        return 'NULL';
    }

    protected function resolveInt(mixed $value): int
    {
        return (int)$value;
    }

    protected function resolveFloat(mixed $value): float
    {
        return (float)$value;
    }

    protected function resolveString(mixed $value): string
    {
        return sprintf(
            "'%s'",
            null !== $this->mysqli
                ? $this->mysqli->real_escape_string($value)
                : addcslashes($value, "'"),
        );
    }

    protected function resolveIdentifier(string $value): string
    {
        $parts = explode('.', $value);
        foreach ($parts as $k => $part) {
            if (!preg_match('/^[\w$\x{0001}-\x{FFFF}]+$/u', $part)) {
                throw new BuilderSpecificatorException($this, 'Not permitted characters in identifier(s)');
            }

            $parts[$k] = sprintf('`%s`', $part);
        }

        return implode('.', $parts);
    }

}