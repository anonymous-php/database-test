<?php
declare(strict_types=1);

namespace FpDbTest\Database;

use mysqli;

interface BuilderFactoryInterface
{

    public function createBuilder(?mysqli $mysqli = null): BuilderInterface;

}