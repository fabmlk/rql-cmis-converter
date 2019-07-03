<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */

namespace Tms\Rql\ParserExtension;

use Tms\Rql\ParserExtension\NodeParser as ExtensionNodeParser;
use Tms\Rql\ParserExtension\NodeParser\Query\ComparisonOperator as ExtensionComparisonOperator;
use Tms\Rql\ParserExtension\NodeParser\Query\FunctionOperator as ExtensionFunctionOperator;
use Tms\Rql\ParserExtension\ValueParser as ExtensionValueParser;
use Xiag\Rql\Parser\NodeParser;
use Xiag\Rql\Parser\NodeParser\Query\ComparisonOperator;
use Xiag\Rql\Parser\NodeParser\Query\LogicalOperator;
use Xiag\Rql\Parser\NodeParserChain;
use Xiag\Rql\Parser\NodeParserInterface;
use Xiag\Rql\Parser\Parser as BaseParser;
use Xiag\Rql\Parser\TypeCaster;
use Xiag\Rql\Parser\ValueParser;

/**
 * Class DqlParser.
 */
class DqlParser extends BaseParser
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
        $fieldAggregateWithValueParser = new ExtensionValueParser\Dql\FieldAggregateWithValueParser($fieldParser, $scalarParser);

        $queryNodeParser = new NodeParser\QueryNodeParser();
        $queryNodeParser
            ->addNodeParser(new NodeParser\Query\GroupNodeParser($queryNodeParser))

            ->addNodeParser(new LogicalOperator\AndNodeParser($queryNodeParser))
            ->addNodeParser(new LogicalOperator\OrNodeParser($queryNodeParser))
            ->addNodeParser(new LogicalOperator\NotNodeParser($queryNodeParser))

            ->addNodeParser(new ComparisonOperator\Rql\InNodeParser($fieldAggregateWithValueParser, $arrayParser))
            ->addNodeParser(new ComparisonOperator\Rql\OutNodeParser($fieldParser, $arrayParser))
            ->addNodeParser(new ComparisonOperator\Rql\EqNodeParser($fieldAggregateWithValueParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\NeNodeParser($fieldAggregateWithValueParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\LtNodeParser($fieldAggregateWithValueParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\GtNodeParser($fieldAggregateWithValueParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\LeNodeParser($fieldAggregateWithValueParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\GeNodeParser($fieldAggregateWithValueParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Rql\LikeNodeParser($fieldAggregateWithValueParser, $globParser))

            ->addNodeParser(new ComparisonOperator\Fiql\InNodeParser($fieldAggregateWithValueParser, $arrayParser))
            ->addNodeParser(new ComparisonOperator\Fiql\OutNodeParser($fieldAggregateWithValueParser, $arrayParser))
            ->addNodeParser(new ComparisonOperator\Fiql\EqNodeParser($fieldAggregateWithValueParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\NeNodeParser($fieldAggregateWithValueParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\LtNodeParser($fieldAggregateWithValueParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\GtNodeParser($fieldAggregateWithValueParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\LeNodeParser($fieldAggregateWithValueParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\GeNodeParser($fieldAggregateWithValueParser, $scalarParser))
            ->addNodeParser(new ComparisonOperator\Fiql\LikeNodeParser($fieldAggregateWithValueParser, $globParser))

            ->addNodeParser(new ExtensionFunctionOperator\Dql\AtDepthWithDepthNodeParser($scalarParser, $integerParser))
            ->addNodeParser(new ExtensionComparisonOperator\BetweenNodeParser($scalarParser));

        return (new NodeParserChain())
            ->addNodeParser($queryNodeParser)
            ->addNodeParser(new ExtensionNodeParser\SelectNodeParser($fieldAggregateWithValueParser))
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
