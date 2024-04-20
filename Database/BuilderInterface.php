<?php
declare(strict_types=1);

namespace FpDbTest\Database;


interface BuilderInterface
{

    public function buildQuery(string $query, array $args = []): string;

}