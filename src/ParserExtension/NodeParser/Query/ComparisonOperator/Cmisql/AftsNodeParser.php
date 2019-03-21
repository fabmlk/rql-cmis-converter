<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Cmis\ParserExtension\NodeParser\Query\ComparisonOperator\Cmisql;

use Tms\Rql\Cmis\ParserExtension\Node\Query\FunctionOperator\Cmisql\AftsNode;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator\AbstractComparisonRqlNodeParser;

/**
 * Class AftsNodeParser.
 *
 * Parser for afts() (stands for 'a'lfresco 'f'ull 't'ext 's'earch)
 */
class AftsNodeParser extends AbstractComparisonRqlNodeParser
{
    /**
     * @inheritdoc
     */
    protected function getOperatorName()
    {
        return 'afts';
    }

    /**
     * @inheritdoc
     */
    protected function createNode($field, $value)
    {
        return new AftsNode($field, $value);
    }
}
