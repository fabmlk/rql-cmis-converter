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
use Tms\Rql\ParserExtension\Node\Query\ScalarOperator\Cmisql\InAnyNode;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\Query\ArrayOperator\InNode;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\AbstractComparisonRqlNodeParser;

/**
 * Class InNodeParser.
 *
 * This parser extends the basic in node parser to support testing with 'ANY' field
 */
class InNodeParser extends AbstractComparisonRqlNodeParser
{
    /**
     * {@inheritdoc}
     */
    public function getOperatorName(): string
    {
        return 'in';
    }

    /**
     * {@inheritdoc}
     */
    protected function createNode($field, $value): AbstractQueryNode
    {
        return $field instanceof AggregateNode
            ? new InAnyNode($field->getField(), $value)
            : new InNode($field, $value);
    }
}
