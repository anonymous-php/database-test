<?php
declare(strict_types=1);

namespace FpDbTest\Database;


interface BlockInterface
{

    public function getValue(): mixed;
    public function setValue(mixed $value): void;
    public function getQuery(): string;
    public function isIntersect(self $range): bool;
    public function getOffset(): int;
    public function getLength(): int;

}