<?php

declare(strict_types=1);

namespace Tms\Rql\Builder;


use Tms\Rql\Visitor\DqlSimpleExpressionVisitor;

class DqlConditionsBuilder extends SqlConditionsBuilder
{
    /**
     * Return default expression visitor.
     *
     * @return callable
     */
    protected function getDefaultExpressionVisitor(): callable
    {
        return new DqlSimpleExpressionVisitor();
    }
}