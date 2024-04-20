<?php
declare(strict_types=1);

namespace FpDbTest\Database\Specificators;


use FpDbTest\Database\BlockInterface;
use FpDbTest\Database\BuilderException;
use Throwable;

class BuilderSpecificatorException extends BuilderException
{

    public function __construct(SpecificatorInterface|BlockInterface $specificator, string $message = "", ?Throwable $previous = null)
    {
        $message = sprintf(
            'Specificator "%s" error at offset %d: %s',
            $specificator->getQuery(),
            $specificator->getOffset(),
            $message,
        );

        parent::__construct($message, 0, $previous);
    }

}