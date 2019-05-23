<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\NodeParser\Query\ComparisonOperator\Cmisql;

use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\AggregateNode;
use Tms\Rql\ParserExtension\Node\Query\ScalarOperator\Cmisql\EqAnyNode;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\Query\ScalarOperator\EqNode;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\AbstractComparisonRqlNodeParser;

/**
 * Class EqNodeParser.
 *
 * This parser extends the basic eq node parser to support equality with 'ANY' field
 */
class EqNodeParser extends AbstractComparisonRqlNodeParser
{
    /**
     * {@inheritdoc}
     */
    public function getOperatorName(): string
    {
        return 'eq';
    }

    /**
     * {@inheritdoc}
     */
    protected function createNode($field, $value): AbstractQueryNode
    {
        return $field instanceof AggregateNode
            ? new EqAnyNode($field->getField(), $value)
            : new EqNode($field, $value);
    }
}
