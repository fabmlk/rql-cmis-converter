<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
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
 * Class SqlParamsExpressionVisitor.
 */
class SqlParamsExpressionVisitor
{
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
                    sprintf('%s <> ?', $node->getField()),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof LtNode:
                return [
                    sprintf('%s < ?', $node->getField()),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof GtNode:
                return [
                    sprintf('%s > ?', $node->getField()),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof GeNode:
                return [
                    sprintf('%s >= ?', $node->getField()),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof LeNode:
                return [
                    sprintf('%s <= ?', $node->getField()),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof InNode:
                return [
                    sprintf('%s IN ?', $node->getField()),
                    $this->encodeValue($node->getValues()),
                ];
            case $node instanceof OutNode:
                return [
                    sprintf('%s NOT IN ?', $node->getField()),
                    $this->encodeValue($node->getValues()),
                ];
            case $node instanceof LikeNode:
                return [
                    sprintf('%s LIKE ?', $node->getField()),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof BetweenNode:
                return [
                    sprintf('%s BETWEEN ? AND ?', $node->getField()),
                    $node->getFrom(),
                    $node->getTo(),
                ];
            case $node instanceof EqNode:
                $encodedValue = $this->encodeValue($node->getValue());
                $operator = 'NULL' === $encodedValue ? 'IS' : '=';

                return [
                    sprintf('%s %s ?', $node->getField(), $operator),
                    $encodedValue,
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
