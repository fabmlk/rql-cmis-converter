<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Cmis\Factory;

use Tms\Rql\Cmis\Builder\QueryBuilderInterface;
use Tms\Rql\Cmis\Builder\SqlQueryBuilder;
use Tms\Rql\Cmis\ConditionsExtension\AbstractEnhanceableConditions;
use Tms\Rql\Cmis\Builder\ConditionsBuilderInterface;
use Tms\Rql\Cmis\ParserExtension\SqlParser;
use Tms\Rql\Cmis\Builder\SqlConditionsBuilder;
use Tms\Rql\Cmis\Visitor\SqlSimpleExpressionVisitor;
use Xiag\Rql\Parser\Parser;

/**
 * Class SqlFactory.
 */
class SqlFactory implements FactoryInterface
{
    /**
     * @const string
     */
    public const TYPE_PARAMS = 'params';

    /**
     * @const string
     */
    public const TYPE_SIMPLE = 'simple';

    /**
     * {@inheritdoc}
     */
    public function getExpressionVisitor(string $type = ''): callable
    {
        switch ($type) {
            case self::TYPE_PARAMS:
                return new SqlSimpleExpressionVisitor();
            case self::TYPE_SIMPLE:
            case '':
                return new SqlSimpleExpressionVisitor();
            default:
                throw new \InvalidArgumentException(sprintf('Unkown visitor type %s', $type));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParser(): Parser
    {
        return new SqlParser();
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionsBuilder(string $type = ''): ConditionsBuilderInterface
    {
        $enhancedConditions = AbstractEnhanceableConditions::make();

        return new SqlConditionsBuilder($enhancedConditions, $this->getExpressionVisitor($type));
    }

    /**
     * {@inheritdoc}
     */
    public function getBuilder(string $visitorType = ''): QueryBuilderInterface
    {
        return new SqlQueryBuilder($this->getConditionsBuilder($visitorType));
    }
}
