<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 *  Pour les informations complètes de copyright et de licence,
 *  veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Visitor;

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
 * Class DqlSimpleExpressionVisitor.
 *
 * https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/dql-doctrine-query-language.html#ebnf
 */
class DqlSimpleExpressionVisitor
{
    /**
     * @var callable
     */
    private $aliasResolver;

    /**
     * DqlSimpleExpressionVisitor constructor.
     *
     * @param callable $aliasResolver
     */
    public function __construct(callable $aliasResolver)
    {
        $this->aliasResolver = $aliasResolver;
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
                $alias = ($this->aliasResolver)($node);
                $field = $node->getField();
                if ($field instanceof AggregateWithValueNode) {
                    return [
                        sprintf('%s(%s.%s) <> %s', \strtoupper($field->getFunction()), $alias, $field->getField(), $this->encodeValue($node->getValue()))
                    ];
                }
                return [
                    sprintf('%s.%s <> %s', $alias, $field, $this->encodeValue($node->getValue())),
                ];
            case $node instanceof LtNode:
                $alias = ($this->aliasResolver)($node);
                $field = $node->getField();
                if ($field instanceof AggregateWithValueNode) {
                    return [
                        sprintf('%s(%s.%s) < %s', \strtoupper($field->getFunction()), $alias, $field->getField(), $this->encodeValue($node->getValue()))
                    ];
                }
                return [
                    sprintf('%s.%s < %s', $alias, $field, $this->encodeValue($node->getValue())),
                ];
            case $node instanceof GtNode:
                $alias = ($this->aliasResolver)($node);
                $field = $node->getField();
                if ($field instanceof AggregateWithValueNode) {
                    return [
                        sprintf('%s(%s.%s) > %s', \strtoupper($field->getFunction()), $alias, $field->getField(), $this->encodeValue($node->getValue()))
                    ];
                }
                return [
                    sprintf('%s.%s > %s', $alias, $field, $this->encodeValue($node->getValue())),
                ];
            case $node instanceof GeNode:
                $alias = ($this->aliasResolver)($node);
                $field = $node->getField();
                if ($field instanceof AggregateWithValueNode) {
                    return [
                        sprintf('%s(%s.%s) >= %s', \strtoupper($field->getFunction()), $alias, $field->getField(), $this->encodeValue($node->getValue()))
                    ];
                }
                return [
                    sprintf('%s.%s >= %s', $alias, $field, $this->encodeValue($node->getValue())),
                ];
            case $node instanceof LeNode:
                $alias = ($this->aliasResolver)($node);
                $field = $node->getField();
                if ($field instanceof AggregateWithValueNode) {
                    return [
                        sprintf('%s(%s.%s) <= %s', \strtoupper($field->getFunction()), $alias, $field->getField(), $this->encodeValue($node->getValue()))
                    ];
                }
                return [
                    sprintf('%s.%s <= %s', $alias, $field, $this->encodeValue($node->getValue())),
                ];
            case $node instanceof InNode:
                $alias = ($this->aliasResolver)($node);
                $field = $node->getField();
                if ($field instanceof AggregateWithValueNode) {
                    return [
                        sprintf('%s(%s.%s) IN %s', \strtoupper($field->getFunction()), $alias, $field->getField(), $this->encodeValue($node->getValues()))
                    ];
                }
                return [
                    sprintf('%s.%s IN %s', $alias, $field, $this->encodeValue($node->getValues())),
                ];
            case $node instanceof OutNode:
                $alias = ($this->aliasResolver)($node);
                $field = $node->getField();
                if ($field instanceof AggregateWithValueNode) {
                    return [
                        sprintf('%s(%s.%s) NOT IN %s', \strtoupper($field->getFunction()), $alias, $field->getField(), $this->encodeValue($node->getValues()))
                    ];
                }
                return [
                    sprintf('%s.%s NOT IN %s', $alias, $field, $this->encodeValue($node->getValues())),
                ];
            case $node instanceof LikeNode:
                $alias = ($this->aliasResolver)($node);
                $field = $node->getField();
                if ($field instanceof AggregateWithValueNode) {
                    return [
                        sprintf('%s(%s.%s) LIKE %s', \strtoupper($field->getFunction()), $alias, $field->getField(), $this->encodeValue($node->getValue()))
                    ];
                }
                return [
                    sprintf('%s.%s LIKE %s', $alias, $field, $this->encodeValue($node->getValue())),
                ];
            case $node instanceof BetweenNode:
                $alias = ($this->aliasResolver)($node);
                return [
                    sprintf('%s.%s BETWEEN %s AND %s', $alias, $node->getField(), $node->getFrom(), $node->getTo()),
                ];
            case $node instanceof EqNode:
                $alias = ($this->aliasResolver)($node);
                $field = $node->getField();
                $encodedValue = $this->encodeValue($node->getValue());
                $operator = 'NULL' === $encodedValue ? 'IS' : '=';

                if ($field instanceof AggregateWithValueNode) {
                    return [
                        sprintf('%s(%s.%s) %s %s', \strtoupper($field->getFunction()), $alias, $field->getField(), $operator, $encodedValue)
                    ];
                }
                return [
                    sprintf('%s.%s %s %s', $alias, $field, $operator, $encodedValue),
                ];
            case $node instanceof AtDepthNode:
                $alias = ($this->aliasResolver)($node);
                return [
                    sprintf('AT_DEPTH(%s,%s,%d)', $alias, $this->encodeValue($node->getLeftValue()), $this->encodeValue($node->getRightValue()))
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
     * @return string
     *
     * @throws \LogicException if $value is not supported
     */
    protected function encodeValue($value): string
    {
        if (\is_array($value)) {
            return '('.implode(',', array_map([$this, 'encodeValue'], $value)).')';
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
            // Do not use var_export here or eventual backslashes will be doubled
            return sprintf("'%s'", $value->toLike());
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        throw new \LogicException(sprintf('Invalid value "%s"', var_export($value, true)));
    }
}
