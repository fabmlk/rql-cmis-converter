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
use Tms\Rql\ParserExtension\Node\Query\ScalarOperator\Cmisql\OutAnyNode;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\Query\ArrayOperator\OutNode;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\AbstractComparisonRqlNodeParser;

/**
 * Class OutNodeParser.
 *
 * This parser extends the basic out node parser to support testing with 'ANY' field
 */
class OutNodeParser extends AbstractComparisonRqlNodeParser
{
    /**
     * {@inheritdoc}
     */
    public function getOperatorName(): string
    {
        return 'out';
    }

    /**
     * {@inheritdoc}
     */
    protected function createNode($field, $value): AbstractQueryNode
    {
        return $field instanceof AggregateNode
            ? new OutAnyNode($field->getField(), $value)
            : new OutNode($field, $value);
    }
}
