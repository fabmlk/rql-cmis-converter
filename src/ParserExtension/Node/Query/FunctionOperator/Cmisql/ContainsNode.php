<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Cmisql;

use Xiag\Rql\Parser\AbstractNode;

/**
 * Class ContainsNode.
 *
 * This node is not used as part of a RQL parsing !
 * It is only used for transmission to the alias resolver,
 * so a decision can be made also based on this type.
 * CMIS QL CONTAINS() function is implemented instead from
 * CmisqlContainsConditions.
 */
class ContainsNode  extends AbstractNode
{
    /**
     * @return string
     */
    public function getNodeName(): string
    {
        return 'contains';
    }
}