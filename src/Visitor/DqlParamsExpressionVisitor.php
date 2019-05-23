<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 *  Pour les informations complètes de copyright et de licence,
 *  veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Visitor;

use Latitude\QueryBuilder\ValueList as in;
use Tms\Rql\ParserExtension\Node\Query\ScalarOperator\BetweenNode;
use Xiag\Rql\Parser\Glob;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\Query\AbstractComparisonOperatorNode;
use Xiag\Rql\Parser\Node\Query\ArrayOperator\InNode;
use Xiag\Rql\Parser\Node\Query\ArrayOperator\OutNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\GeNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\GtNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\LeNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\LikeNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\LtNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\NeNode;

/**
 * Class DqlParamsExpressionVisitor.
 *
 * https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/dql-doctrine-query-language.html#ebnf
 */
class DqlParamsExpressionVisitor
{
    /**
     * @var string
     */
    private $rootAlias;

    /**
     * @var int
     */
    private $placeholderInc = 0;

    /**
     * DqlSimpleExpressionVisitor constructor.
     *
     * @param string $rootAlias
     */
    public function __construct(string $rootAlias)
    {
        $this->rootAlias = $rootAlias;
    }

    /**
     * @param AbstractComparisonOperatorNode $node
     *
     * @return array
     *
     * @throws \DomainException
     */
    public function __invoke(AbstractQueryNode $node): array
    {
        switch (true) {
            case $node instanceof NeNode:
                return [
                    sprintf('%s.%s <> ?%d', $this->rootAlias, $node->getField(), $this->placeholderInc++),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof LtNode:
                return [
                    sprintf('%s.%s < ?%d', $this->rootAlias, $node->getField(), $this->placeholderInc++),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof GtNode:
                return [
                    sprintf('%s.%s > ?%d', $this->rootAlias, $node->getField(), $this->placeholderInc++),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof GeNode:
                return [
                    sprintf('%s.%s >= ?%d', $this->rootAlias, $node->getField(), $this->placeholderInc++),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof LeNode:
                return [
                    sprintf('%s.%s <= ?%d', $this->rootAlias, $node->getField(), $this->placeholderInc++),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof InNode:
                return [
                    sprintf('%s.%s IN ?%d', $this->rootAlias, $node->getField(), $this->placeholderInc++),
                    $this->encodeValue($node->getValues()),
                ];
            case $node instanceof OutNode:
                return [
                    sprintf('%s.%s NOT IN ?%d', $this->rootAlias, $node->getField(), $this->placeholderInc++),
                    $this->encodeValue($node->getValues()),
                ];
            case $node instanceof LikeNode:
                return [
                    sprintf('%s.%s LIKE ?%d', $this->rootAlias, $node->getField(), $this->placeholderInc++),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof BetweenNode:
                return [
                    sprintf('%s.%s BETWEEN ?%d AND ?%d', $this->rootAlias, $node->getField(), $this->placeholderInc++, $this->placeholderInc++),
                ];
            case $node instanceof EqNode:
                $encodedValue = $this->encodeValue($node->getValue());
                $operator = 'NULL' === $encodedValue ? 'IS' : '=';

                return [
                    sprintf('%s.%s %s ?%d', $this->rootAlias, $node->getField(), $operator, $this->placeholderInc++),
                    $encodedValue
                ];
            default:
                throw new \DomainException(sprintf('Unknown node %s', get_class($node)));
        }
    }

    /**
     * Encode value to be SQL-compatible.
     *
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws \LogicException if $value is not supported
     */
    protected function encodeValue($value)
    {
        if (\is_array($value)) {
            return in::make(
                \array_map(
                    function ($item) {
                        // for instance: if a DateTime is in the list
                        return \is_object($item) ? $this->encodeValue($item) : $item;
                    },
                    $value
                )
            );
        }
        if (\is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }
        if (\is_null($value)) {
            return 'NULL';
        }
        if (\is_float($value) || \is_int($value)) {
            return (string) $value;
        }
        if (\is_string($value)) {
            return var_export($value, true);
        }
        if ($value instanceof Glob) {
            return var_export($value->toLike(), true);
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        throw new \LogicException(sprintf('Invalid value "%s"', var_export($value, true)));
    }
}
