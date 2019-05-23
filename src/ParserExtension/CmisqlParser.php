<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

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
 * Class CmisqlParser.
 */
class CmisqlParser extends BaseParser
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

        $containsQueryNodeParser = new NodeParser\QueryNodeParser();
        $containsQueryNodeParser
            ->addNodeParser(new LogicalOperator\AndNodeParser($containsQueryNodeParser))
            ->addNodeParser(new LogicalOperator\OrNodeParser($containsQueryNodeParser))
            // accept globs or strings: globs are allowed for Coe and Nce nodes but they will be converted to string
            ->addNodeParser(new ExtensionFunctionOperator\Cmisql\CoeNodeParser($globParser))
            ->addNodeParser(new ExtensionFunctionOperator\Cmisql\NceNodeParser($globParser))
            // accept globs or strings
            ->addNodeParser(new ExtensionFunctionOperator\Cmisql\ColNodeParser($globParser))
            ->addNodeParser(new ExtensionFunctionOperator\Cmisql\NclNodeParser($globParser))
            ->addNodeParser(new ExtensionComparisonOperator\Cmisql\AftsNodeParser($fieldParser, $globParser));

        $containsParser = new ExtensionFunctionOperator\Cmisql\ContainsNodeParser(
            new NodeParser\Query\GroupNodeParser($containsQueryNodeParser)
        );

        $queryNodeParser = new NodeParser\QueryNodeParser();
        $queryNodeParser
            ->addNodeParser(new NodeParser\Query\GroupNodeParser($queryNodeParser))
            ->addNodeParser(new LogicalOperator\AndNodeParser($queryNodeParser))
            ->addNodeParser(new LogicalOperator\OrNodeParser($queryNodeParser))
            ->addNodeParser(new LogicalOperator\NotNodeParser($queryNodeParser))
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
            ->addNodeParser($containsParser)
            ->addNodeParser(new ExtensionFunctionOperator\Cmisql\InTreeNodeParser($scalarParser))
            ->addNodeParser(new ExtensionFunctionOperator\Cmisql\InFolderNodeParser($scalarParser))
            ->addNodeParser(new ExtensionComparisonOperator\BetweenNodeParser($scalarParser))
            ->addNodeParser(new ExtensionComparisonOperator\Cmisql\EqNodeParser($fieldAggregateParser, $scalarParser))
            ->addNodeParser(new ExtensionComparisonOperator\Cmisql\InNodeParser($fieldAggregateParser, $arrayParser))
            ->addNodeParser(new ExtensionComparisonOperator\Cmisql\OutNodeParser($fieldAggregateParser, $arrayParser));

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
