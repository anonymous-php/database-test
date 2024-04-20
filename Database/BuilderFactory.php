<?php
declare(strict_types=1);

namespace FpDbTest\Database;


use FpDbTest\Database\Specificators\ArraySpecificator;
use FpDbTest\Database\Specificators\CommonSpecificator;
use FpDbTest\Database\Specificators\FloatSpecificator;
use FpDbTest\Database\Specificators\IdentifierSpecificator;
use FpDbTest\Database\Specificators\IntegerSpecificator;
use FpDbTest\Database\Specificators\SpecificatorFactory;
use mysqli;

class BuilderFactory implements BuilderFactoryInterface
{

    public function createBuilder(?mysqli $mysqli = null): BuilderInterface
    {
        return new Builder($mysqli, [
            '?' => CommonSpecificator::class,
            '?d' => IntegerSpecificator::class,
            '?f' => FloatSpecificator::class,
            '?a' => ArraySpecificator::class,
            '?#' => IdentifierSpecificator::class,
        ], new SpecificatorFactory());
    }

}