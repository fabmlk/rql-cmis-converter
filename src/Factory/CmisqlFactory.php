<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Factory;

use Tms\Rql\Builder\CmisqlConditionsBuilder;
use Tms\Rql\Builder\CmisqlQueryBuilder;
use Tms\Rql\Builder\ConditionsBuilderInterface;
use Tms\Rql\Builder\QueryBuilderInterface;
use Tms\Rql\Builder\SqlConditionsBuilder;
use Tms\Rql\ConditionsExtension\AbstractEnhanceableConditions;
use Tms\Rql\ConditionsExtension\CmisqlContainsConditions;
use Tms\Rql\ParserExtension\CmisqlParser;
use Tms\Rql\Visitor\CmisqlParamsExpressionVisitor;
use Tms\Rql\Visitor\CmisqlSimpleExpressionVisitor;
use Xiag\Rql\Parser\Parser;

/**
 * Class CmisqlFactory.
 */
class CmisqlFactory implements FactoryInterface
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
     * @var callable|\Closure
     */
    private $aliasResolver;

    /**
     * CmisqlFactory constructor.
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
                return new CmisqlParamsExpressionVisitor($this->aliasResolver);
            case self::TYPE_SIMPLE:
            case '':
                return new CmisqlSimpleExpressionVisitor($this->aliasResolver);
            default:
                throw new \InvalidArgumentException(sprintf('Unkown visitor type %s', $type));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParser(): Parser
    {
        return new CmisqlParser();
    }

    /**
     * {@inheritdoc}
     *
     * @return ConditionsBuilderInterface|SqlConditionsBuilder
     */
    public function getConditionsBuilder(string $type = ''): ConditionsBuilderInterface
    {
        /** @var AbstractEnhanceableConditions $enhancedConditions */
        $enhancedConditions = CmisqlContainsConditions::make();
        $enhancedConditions->withAliasResolver($this->aliasResolver);

        return new CmisqlConditionsBuilder($this->aliasResolver, $enhancedConditions, $this->getExpressionVisitor($type));
    }

    /**
     * {@inheritdoc}
     */
    public function getBuilder(string $visitorType = ''): QueryBuilderInterface
    {
        return new CmisqlQueryBuilder($this->getConditionsBuilder($visitorType), $this->aliasResolver);
    }
}
