<?php

declare(strict_types=1);

namespace Tms\Rql\Builder;


use Tms\Rql\ConditionsExtension\AbstractEnhanceableConditions;
use Tms\Rql\Visitor\DqlSimpleExpressionVisitor;

/**
 * Class DqlConditionsBuilder.
 */
class DqlConditionsBuilder extends SqlConditionsBuilder
{
    /**
     * @var string
     */
    private $rootAlias;

    /**
     * DqlConditionsBuilder constructor.
     *
     * @param string                        $rootAlias
     * @param AbstractEnhanceableConditions $conditions
     * @param callable|null                 $expressionVisitor
     */
    public function __construct(string $rootAlias, AbstractEnhanceableConditions $conditions, callable $expressionVisitor = null)
    {
        $this->rootAlias = $rootAlias;
        parent::__construct($conditions, $expressionVisitor);
    }

    /**
     * Return default expression visitor.
     *
     * @return callable
     */
    protected function getDefaultExpressionVisitor(): callable
    {
        return new DqlSimpleExpressionVisitor($this->rootAlias);
    }
}