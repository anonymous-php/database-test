<?php
declare(strict_types=1);

namespace FpDbTest\Database;


use FpDbTest\Database\Specificators\AbstractSpecificator;
use FpDbTest\Database\Specificators\SpecificatorFactory;
use FpDbTest\Database\Specificators\SpecificatorFactoryInterface;
use mysqli;

class Builder implements BuilderInterface
{

    private ?mysqli $mysqli;
    private array $definitions;
    private SpecificatorFactoryInterface $specificatorFactory;


    public function __construct(?mysqli $mysqli, array $definitions, ?SpecificatorFactoryInterface $specificatorFactory = null)
    {
        $this->mysqli = $mysqli;
        $this->definitions = $definitions;
        $this->specificatorFactory = $specificatorFactory ?? new SpecificatorFactory();
    }

    public function buildQuery(string $query, array $args = []): string
    {
        if (count($this->definitions) === 0) {
            return $query;
        }

        $query = $this->escapeLiterals($query, $literals);
        $query = $this->applySpecificators($query, $args);
        $query = $this->applyBlocks($query);
        $query = $this->applyLiterals($query, $literals);

        if (str_contains($query, AbstractSpecificator::SKIP)) {
            throw new BuilderException('Skip value outside blocks');
        }

        return $query;
    }

    public function escapeLiterals(string $query, ?array &$literals = []): string
    {
        $literals = [];

        return preg_replace_callback(
            '/((\"\")|(\".*?[^\\\]\"))|((\\\'\\\')|(\\\'.*?[^\\\]\\\'))/ms',
            function ($match) use (&$literals) {
                $i = count($literals);
                $literals[$i] = $match[0];
                return sprintf("\0%d\0", $i);
            },
            $query,
        );
    }

    private function applySpecificators(string $query, array $args): string
    {
        $keys = array_keys($this->definitions);

        rsort($keys);

        $pattern = implode('|', array_map(function (string $specificator) {
            return preg_quote($specificator, '/');
        }, $keys));

        return preg_replace_callback('/(' . $pattern . ')/', function ($match) use (&$args) {
            $value = array_shift($args);
            $name = $match[0][0];

            $specificator = $this->specificatorFactory->createSpecificator($this->definitions[$name], $name, $match[0][1]);
            $specificator->resolve($value, $this->mysqli);

            return $specificator->getValue();
        }, $query, -1, $count, PREG_OFFSET_CAPTURE);
    }

    private function applyBlocks(string $query): string
    {
        do {
            $query = preg_replace_callback('/\{((?!(\{|\})).)*\}/ms', function ($match) {
                if (str_contains($match[0], AbstractSpecificator::SKIP)) {
                    return '';
                }

                return substr($match[0], 1, -1);
            }, $query, -1, $count);
        } while ($count > 0);

        return $query;
    }

    private function applyLiterals(string $query, array $literals): string
    {
        return preg_replace_callback("/\0\d+\0/ms", function ($match) use ($literals) {
            return $literals[trim($match[0], "\0")];
        }, $query);
    }

}