<?php
declare(strict_types=1);

namespace FpDbTest\Database;


use FpDbTest\Database\Specificators\BuilderSpecificatorException;
use FpDbTest\Database\Specificators\SpecificatorFactory;
use FpDbTest\Database\Specificators\SpecificatorFactoryInterface;
use FpDbTest\Database\Specificators\SpecificatorInterface;
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

        return $this->buildQueryInternal($query, $args);
    }

    private function buildQueryInternal(string $query, array $args = [], int $level = 0): string
    {
        $specificators = $this->getSpecificators($query);

        if (count($specificators) !== count($args)) {
            throw new BuilderException('Count of specificators doesn\'t equal count of arguments');
        }

        $block = $this->getBlock($query);

        if (null !== $block) {
            $blockArgs = [];
            foreach ($specificators as $k => $specificator) {
                if ($specificator->isIntersect($block)) {
                    $blockArgs[] = $args[$k];
                    unset($specificators[$k], $args[$k]);
                }
            }

            $query = substr($query, 0, $block->getOffset())
                . $this->buildQueryInternal(substr($block->getQuery(), 1, -1), $blockArgs, $level + 1)
                . substr($query, $block->getOffset() + $block->getLength());

            return $this->buildQueryInternal($query, $args);
        }

        if (count($specificators) === 0) {
            return $query;
        }

        $specificator = array_shift($specificators);
        $specificator->resolve(array_shift($args), $this->mysqli);

        $value = $specificator->getValue();

        if ($value instanceof Skip) {
            if (0 === $level) {
                throw new BuilderSpecificatorException($specificator, 'Skip value outside block');
            }

            return '';
        }

        $query = substr($query, 0, $specificator->getOffset())
            . $value
            . substr($query, $specificator->getOffset() + $specificator->getLength());

        return count($specificators)
            ? $this->buildQueryInternal($query, $args)
            : $query;
    }

    /**
     * @return SpecificatorInterface[]
     */
    private function getSpecificators(string $query): array
    {
        $keys = array_keys($this->definitions);

        rsort($keys);

        $pattern = implode('|', array_map(function (string $specificator) {
            return preg_quote($specificator, '/');
        }, $keys));

        if (!preg_match_all("/({$pattern})/ms", $this->escapeLiterals($query), $matches, PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $specificators = [];

        foreach ($matches[0] as $match) {
            $specificators[] = $this->createSpecificator($match[0], $match[1]);
        }

        return $specificators;
    }

    private function escapeLiterals(string $query): string
    {
        if (!preg_match_all('/((\"\")|(\".*?[^\\\]\"))|((\\\'\\\')|(\\\'.*?[^\\\]\\\'))/ms', $query, $matches, PREG_OFFSET_CAPTURE)) {
            return $query;
        }

        foreach ($matches[0] as $match) {
            $length = strlen($match[0]);

            $query = substr($query, 0, $match[1])
                . str_pad('', $length, chr(0))
                . substr($query, $match[1] + $length);
        }

        return $query;
    }

    private function createSpecificator(string $specificator, int $offset): SpecificatorInterface
    {
        return $this->specificatorFactory
            ->createSpecificator($this->definitions[$specificator], $specificator, $offset, strlen($specificator));
    }

    private function getBlock(string $query): ?BlockInterface
    {
        if (!preg_match('/\{((?!(\{|\})).)*\}/ms', $this->escapeLiterals($query), $matches, PREG_OFFSET_CAPTURE)) {
            return null;
        }

        $offset = (int)$matches[0][1];

        return new Block(substr($query, $offset, strlen($matches[0][0])), $offset, strlen($matches[0][0]));
    }

}