<?php
declare(strict_types=1);

namespace FpDbTest\Database;


class Block implements BlockInterface
{

    private mixed $value;

    private string $query;

    private int $offset;
    private int $length;


    public function __construct(string $query, int $offset = 0)
    {
        $this->query = $query;
        $this->offset = $offset;
        $this->length = strlen($query);
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function isIntersect(BlockInterface $range): bool
    {
        return ($this->getOffset() - ($range->getOffset() + $range->getLength())) * ($range->getOffset() - ($this->getOffset() + $this->getLength())) > 0;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getLength(): int
    {
        return $this->length;
    }

}