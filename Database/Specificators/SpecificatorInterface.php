<?php
declare(strict_types=1);

namespace FpDbTest\Database\Specificators;


use mysqli;

interface SpecificatorInterface
{

    public function resolve(mixed $value, ?mysqli $mysqli = null): void;

}