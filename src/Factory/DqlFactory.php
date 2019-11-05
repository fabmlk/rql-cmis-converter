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
     * @var callable|null
     */
    private $aliasResolver;

    /**
     * DqlFactory constructor.
     *
     * @param callable|null $aliasResolver a callable that returns the alias
     */
    public function __construct($aliasResolver = null)
    {
        if (!$aliasResolver) {
            $aliasResolver = static function ($node, $alias) { return $alias; };
        }

        $this->setAliasResolver($aliasResolver);
    }

    /**
     * {@inheritDoc}
     */
    public function setAliasResolver(callable $aliasResolver): void
    {
        $this->aliasResolver = new AliasResolverWrapper($aliasResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpressionVisitor(string $type = ''): callable
    {
        switch ($type) {
            case self::TYPE_PARAMS:
                return new DqlParamsExpressionVisitor($this->aliasResolver);
            case self::TYPE_SIMPLE:
            case '':
                return new DqlSimpleExpressionVisitor($this->aliasResolver);
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
        $enhancedConditions->withAliasResolver($this->aliasResolver);

        return new DqlConditionsBuilder($this->aliasResolver, $enhancedConditions, $this->getExpressionVisitor($type));
    }

    /**
     * {@inheritdoc}
     */
    public function getBuilder(string $visitorType = ''): QueryBuilderInterface
    {
        return new DqlQueryBuilder($this->getConditionsBuilder($visitorType), $this->aliasResolver);
    }
}
