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
     * Return default expression visitor.
     *
     * @return callable
     */
    protected function getDefaultExpressionVisitor(): callable
    {
        return new DqlSimpleExpressionVisitor($this->aliasResolver);
    }
}