<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 *  Pour les informations complètes de copyright et de licence,
 *  veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Visitor;

use Tms\Rql\Builder\DqlIndexedPlaceholderValueList as in;
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Dql\AggregateWithValueNode;
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Dql\AtDepthNode;
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
                $field = $node->getField();
                if ($field instanceof AggregateWithValueNode) {
                    return [
                        sprintf('%s(%s.%s) <> ?%d', \strtoupper($field->getFunction()), $this->rootAlias, $field->getField(), $this->placeholderInc++),
                        $this->encodeValue($node->getValue()),
                    ];
                }
                return [
                    sprintf('%s.%s <> ?%d', $this->rootAlias, $node->getField(), $this->placeholderInc++),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof LtNode:
                $field = $node->getField();
                if ($field instanceof AggregateWithValueNode) {
                    return [
                        sprintf('%s(%s.%s) < ?%d', \strtoupper($field->getFunction()), $this->rootAlias, $field->getField(), $this->placeholderInc++),
                        $this->encodeValue($node->getValue()),
                    ];
                }
                return [
                    sprintf('%s.%s < ?%d', $this->rootAlias, $node->getField(), $this->placeholderInc++),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof GtNode:
                $field = $node->getField();
                if ($field instanceof AggregateWithValueNode) {
                    return [
                        sprintf('%s(%s.%s) > ?%d', \strtoupper($field->getFunction()), $this->rootAlias, $field->getField(), $this->placeholderInc++),
                        $this->encodeValue($node->getValue()),
                    ];
                }
                return [
                    sprintf('%s.%s > ?%d', $this->rootAlias, $node->getField(), $this->placeholderInc++),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof GeNode:
                $field = $node->getField();
                if ($field instanceof AggregateWithValueNode) {
                    return [
                        sprintf('%s(%s.%s) >= ?%d', \strtoupper($field->getFunction()), $this->rootAlias, $field->getField(), $this->placeholderInc++),
                        $this->encodeValue($node->getValue()),
                    ];
                }
                return [
                    sprintf('%s.%s >= ?%d', $this->rootAlias, $node->getField(), $this->placeholderInc++),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof LeNode:
                $field = $node->getField();
                if ($field instanceof AggregateWithValueNode) {
                    return [
                        sprintf('%s(%s.%s) <= ?%d', \strtoupper($field->getFunction()), $this->rootAlias, $field->getField(), $this->placeholderInc++),
                        $this->encodeValue($node->getValue()),
                    ];
                }
                return [
                    sprintf('%s.%s <= ?%d', $this->rootAlias, $node->getField(), $this->placeholderInc++),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof InNode:
                $field = $node->getField();
                if ($field instanceof AggregateWithValueNode) {
                    $result = [
                        sprintf('%s(%s.%s) IN ?', \strtoupper($field->getFunction()), $this->rootAlias, $field->getField()),
                        $this->encodeValue($node->getValues()),
                    ];
                } else {
                    $result = [
                        sprintf('%s.%s IN ?', $this->rootAlias, $node->getField()),
                        $this->encodeValue($node->getValues()),
                    ];
                }
                $this->placeholderInc += count($node->getValues());

                return $result;
            case $node instanceof OutNode:
                $field = $node->getField();
                if ($field instanceof AggregateWithValueNode) {
                    $result = [
                        sprintf('%s(%s.%s) NOT IN ?', \strtoupper($field->getFunction()), $this->rootAlias, $field->getField()),
                        $this->encodeValue($node->getValues()),
                    ];
                } else {
                    $result = [
                        sprintf('%s.%s NOT IN ?', $this->rootAlias, $node->getField()),
                        $this->encodeValue($node->getValues()),
                    ];
                }
                $this->placeholderInc += count($node->getValues());

                return $result;
            case $node instanceof LikeNode:
                $field = $node->getField();
                if ($field instanceof AggregateWithValueNode) {
                    return [
                        sprintf('%s(%s.%s) LIKE ?%d', \strtoupper($field->getFunction()), $this->rootAlias, $field->getField(), $this->placeholderInc++),
                        $this->encodeValue($node->getValue()),
                    ];
                }
                return [
                    sprintf('%s.%s LIKE ?%d', $this->rootAlias, $node->getField(), $this->placeholderInc++),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof BetweenNode:
                return [
                    sprintf('%s.%s BETWEEN ?%d AND ?%d', $this->rootAlias, $node->getField(), $this->placeholderInc++, $this->placeholderInc++),
                ];
            case $node instanceof EqNode:
                $field = $node->getField();
                $encodedValue = $this->encodeValue($node->getValue());
                $operator = 'NULL' === $encodedValue ? 'IS' : '=';

                if ($field instanceof AggregateWithValueNode) {
                    return [
                        sprintf('%s(%s.%s) %s ?%d', \strtoupper($field->getFunction()), $this->rootAlias, $field->getField(), $operator, $this->placeholderInc++),
                        $encodedValue
                    ];
                }
                return [
                    sprintf('%s.%s %s ?%d', $this->rootAlias, $node->getField(), $operator, $this->placeholderInc++),
                    $encodedValue
                ];
            case $node instanceof AtDepthNode:
                return [
                    sprintf('AT_DEPTH(?%d,?%d)', $this->placeholderInc++, $this->placeholderInc++),
                    $this->encodeValue($node->getLeftValue()),
                    $this->encodeValue($node->getRightValue())
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
                ),
                $this->placeholderInc
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
