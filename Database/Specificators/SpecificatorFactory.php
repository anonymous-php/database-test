<?php
declare(strict_types=1);

namespace FpDbTest\Database\Specificators;


use FpDbTest\Database\BuilderException;

class SpecificatorFactory implements SpecificatorFactoryInterface
{

    public function createSpecificator(string $class, ...$data): SpecificatorInterface
    {
        $specificator = new $class(...$data);

        if (!$specificator instanceof SpecificatorInterface) {
            throw new BuilderException('Provided class doesn\'t implement SpecificatorInerface');
        }

        return $specificator;
    }

}