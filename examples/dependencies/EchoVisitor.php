<?php

declare(strict_types=1);

use Tms\Rql\Builder\VisitExpressionListenerInterface;
use Xiag\Rql\Parser\AbstractNode;

class EchoVisitor implements VisitExpressionListenerInterface
{
    public function update(AbstractNode &$node): void {
        if (method_exists($node, 'getField')) {
            echo "Visiting field: " . $node->getField() . PHP_EOL;
        }
    }
}