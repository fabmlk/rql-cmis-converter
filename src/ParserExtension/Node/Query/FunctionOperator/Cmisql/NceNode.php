<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Cmis\ParserExtension\Node\Query\FunctionOperator\Cmisql;

use Tms\Rql\Cmis\ParserExtension\Node\Query\FunctionOperator\AbstractUnaryNode;

/**
 * Class NceNode.
 */
class NceNode extends AbstractUnaryNode
{
    /**
     * @return string
     */
    public function getNodeName(): string
    {
        return 'nce';
    }
}
