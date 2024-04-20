<?php

namespace FpDbTest;

use FpDbTest\Database\Block;
use FpDbTest\Database\Builder;
use FpDbTest\Database\BuilderFactory;
use mysqli;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function buildQuery(string $query, array $args = []): string
    {
        /** @var Builder $builder */
        $builder = (new BuilderFactory())->createBuilder($this->mysqli);

        return $builder->buildQuery($query, $args);
    }

    public function skip()
    {
        return Block::SKIP;
    }
}
