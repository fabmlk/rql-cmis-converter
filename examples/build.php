<?php

use Tms\Rql\Factory\CmisqlFactory;

require 'rql-complex.php';

$builder = $factory->getBuilder(CmisqlFactory::TYPE_PARAMS);

$builder->onVisitExpression(new class() implements \Tms\Rql\Builder\VisitExpressionListenerInterface {
    public function update(\Xiag\Rql\Parser\AbstractNode &$node): void {
        if (method_exists($node, 'getField')) {
            echo $node->getField() . PHP_EOL;
        }
    }
});

$query = $builder->build($tree, 'cmis:document');
echo $query->sql();
echo SqlFormatter::format($query->sql());
var_dump($query->params());

