<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Cmisql;

use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\AbstractUnaryNode;

/**
 * Class ColNode.
 */
class ColNode extends AbstractUnaryNode
{
    /**
     * @return string
     */
    public function getNodeName(): string
    {
        return 'col';
    }
}
