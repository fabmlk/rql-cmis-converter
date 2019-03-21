<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */

namespace Tms\Rql\Cmis\ParserExtension;

use Tms\Rql\Cmis\ParserExtension\NodeParser as ExtensionNodeParser;
use Tms\Rql\Cmis\ParserExtension\NodeParser\Query\ComparisonOperator as ExtensionComparisonOperator;
use Tms\Rql\Cmis\ParserExtension\ValueParser as ExtensionValueParser;
use Xiag\Rql\Parser\NodeParser;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator;
use Xiag\Rql\Parser\NodeParser\Query\LogicalOperator;
use Xiag\Rql\Parser\NodeParserChain;
use Xiag\Rql\Parser\NodeParserInterface;
use Xiag\Rql\Parser\Parser as BaseParser;
use Xiag\Rql\Parser\TypeCaster;
use Xiag\Rql\Parser\ValueParser;

/**
 * Class SqlParser.
 */
class SqlParser extends BaseParser
{
    /**
     * @return \Xiag\Rql\Parser\NodeParserInterface
     */
    public static function createDefaultNodeParser(): NodeParserInterface
    {
        $scalarParser = (new ValueParser\ScalarParser())
            ->registerTypeCaster('string', new TypeCaster\StringTypeCaster())
            ->registerTypeCaster('integer', new TypeCaster\IntegerTypeCaster())
            ->registerTypeCaster('float', new TypeCaster\FloatTypeCaster())
            ->registerTypeCaster('boolean', new TypeCaster\BooleanTypeCaster());
        $arrayParser = new ValueParser\ArrayParser($scalarParser);
        $globParser = new ValueParser\GlobParser();
        $fieldParser = new ValueParser\FieldParser();
        $integerParser = new ValueParser\IntegerParser();
        $fieldAggregateParser = new ExtensionValueParser\FieldAggregateParser($fieldParser);

        $queryNodeParser = new NodeParser\QueryNodeParser();
        $queryNodeParser
            ->addNodeParser(new NodeParser\Query\GroupNodeParser($queryNodeParser))

            ->addNodeParser(new LogicalOperator\AndNodeParser($queryNodeParser))
            ->addNodeParser(new LogicalOperator\OrNodeParser($queryNodeParser))
            ->addNodeParser(new LogicalOperator\NotNodeParser($queryNodeParser))

            ->addNodeParser(new ComparisonOperator\Rql\InNodeParser($fieldParser, $arrayParser))
            ->addNodeParser(new ComparisonOperator\Rql\OutNodeParser($fieldParser, $arrayParser))
            ->addNodeParser(new ComparisonOperator\Rql\EqNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\NeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\LtNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\GtNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\LeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\GeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\LikeNodeParser($fieldParser, $globParser))

            ->addNodeParser(new ComparisonOperator\Fiql\InNodeParser($fieldParser, $arrayParser))
            ->addNodeParser(new ComparisonOperator\Fiql\OutNodeParser($fieldParser, $arrayParser))
            ->addNodeParser(new ComparisonOperator\Fiql\EqNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\NeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\LtNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\GtNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\LeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\GeNodeParser($fieldParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\LikeNodeParser($fieldParser, $globParser))

            ->addNodeParser(new ExtensionComparisonOperator\BetweenNodeParser($scalarParser));

        return (new NodeParserChain())
            ->addNodeParser($queryNodeParser)
            ->addNodeParser(new ExtensionNodeParser\SelectNodeParser($fieldAggregateParser))
            ->addNodeParser(new ExtensionNodeParser\GroupbyNodeParser())
            ->addNodeParser(new NodeParser\SortNodeParser($fieldParser))
            ->addNodeParser(new NodeParser\LimitNodeParser($integerParser));
    }

    /**
     * @return SqlQueryBuilder
     */
    protected function createQueryBuilder()
    {
        return new SqlQueryBuilder();
    }
}
