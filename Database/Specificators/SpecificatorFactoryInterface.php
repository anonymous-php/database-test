<?php
declare(strict_types=1);

namespace FpDbTest\Database\Specificators;


interface SpecificatorFactoryInterface
{

    public function createSpecificator(string $class, ...$data): SpecificatorInterface;

}