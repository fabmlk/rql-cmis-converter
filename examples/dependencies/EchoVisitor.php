<?php

declare(strict_types=1);

use Tms\Rql\Builder\VisitExpressionListenerInterface;
use Xiag\Rql\Parser\AbstractNode;

/**
 * Class EchoVisitor
 */
class EchoVisitor implements VisitExpressionListenerInterface
{
    /**
     * @param AbstractNode $node
     *
     * @return AbstractNode
     */
    public function update(AbstractNode $node) {
        if (method_exists($node, 'getField')) {
            echo "Visiting field: " . $node->getField() . PHP_EOL;
        }
    }
}