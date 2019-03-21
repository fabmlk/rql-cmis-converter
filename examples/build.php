<?php

use Tms\Rql\Cmis\Factory\CmisqlFactory;

function showoff($rql, $tree) {
    global $factory;

    echo 'RQL:' . PHP_EOL
        . '----' . PHP_EOL
        . $rql . PHP_EOL;
    echo PHP_EOL;

    $builder = $factory->getBuilder(CmisqlFactory::TYPE_SIMPLE);


    echo 'VISIT:' . PHP_EOL
        . '------' . PHP_EOL;
    $builder->onVisitExpression(new class() implements \Tms\Rql\Cmis\Builder\VisitExpressionListenerInterface {
        public function update(\Xiag\Rql\Parser\AbstractNode &$node): void {
            if (method_exists($node, 'getField')) {
                echo "Visiting field: " . $node->getField() . PHP_EOL;
            }
        }
    });

    $query = $builder->build($tree, 'cmis:document');
    echo PHP_EOL;
    echo 'SQL:' . PHP_EOL
        . '----' . PHP_EOL
        . SqlFormatter::format($query->sql()) . PHP_EOL;
    echo PHP_EOL;

    echo 'PARAMS:' . PHP_EOL
        . '-------' . PHP_EOL;
    var_dump($query->params());
    echo PHP_EOL;
}


require 'rql-complex.php';
showoff($rql, $tree);

require 'rql-multiple-select.php';
showoff($rql, $tree);
