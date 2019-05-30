<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Factory;

use Tms\Rql\Builder\ConditionsBuilderInterface;
use Tms\Rql\Builder\DqlConditionsBuilder;
use Tms\Rql\Builder\DqlQueryBuilder;
use Tms\Rql\Builder\QueryBuilderInterface;
use Tms\Rql\Builder\SqlConditionsBuilder;
use Tms\Rql\ConditionsExtension\AbstractEnhanceableConditions;
use Tms\Rql\ConditionsExtension\SqlNotConditions;
use Tms\Rql\ParserExtension\DqlParser;
use Tms\Rql\Visitor\DqlParamsExpressionVisitor;
use Tms\Rql\Visitor\DqlSimpleExpressionVisitor;
use Xiag\Rql\Parser\Parser;

/**
 * Class DqlFactory.
 */
class DqlFactory implements FactoryInterface
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
     * @const string
     */
    public const DEFAULT_ROOT_ALIAS = 'o';

    /**
     * The entity alias involved in the construction of the query
     *
     * @var string $rootAlias
     */
    private $rootAlias;

    /**
     * DqlFactory constructor.
     *
     * @param string $rootAlias
     */
    public function __construct(string $rootAlias = self::DEFAULT_ROOT_ALIAS)
    {
       $this->rootAlias = $rootAlias;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpressionVisitor(string $type = ''): callable
    {
        switch ($type) {
            case self::TYPE_PARAMS:
                return new DqlParamsExpressionVisitor($this->rootAlias);
            case self::TYPE_SIMPLE:
            case '':
                return new DqlSimpleExpressionVisitor($this->rootAlias);
            default:
                throw new \InvalidArgumentException(sprintf('Unkown visitor type %s', $type));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParser(): Parser
    {
        return new DqlParser();
    }

    /**
     * {@inheritdoc}
     *
     * @return ConditionsBuilderInterface|SqlConditionsBuilder
     */
    public function getConditionsBuilder(string $type = ''): ConditionsBuilderInterface
    {
        /** @var AbstractEnhanceableConditions $enhancedConditions */
        $enhancedConditions = SqlNotConditions::make();

        return new DqlConditionsBuilder($this->rootAlias, $enhancedConditions, $this->getExpressionVisitor($type));
    }

    /**
     * {@inheritdoc}
     */
    public function getBuilder(string $visitorType = ''): QueryBuilderInterface
    {
        return new DqlQueryBuilder($this->getConditionsBuilder($visitorType), $this->rootAlias);
    }
}
