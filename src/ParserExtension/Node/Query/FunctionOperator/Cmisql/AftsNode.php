<?php

declare(strict_types=1);

namespace Tms\Rql\Cmis\ParserExtension\Node\Query\FunctionOperator\Cmisql;


use Xiag\Rql\Parser\Node\Query\AbstractScalarOperatorNode;

/**
 * Class AftsNode.
 */
class AftsNode extends AbstractScalarOperatorNode
{
    /**
     * @inheritdoc
     */
    public function getNodeName()
    {
        return 'afts';
    }
}