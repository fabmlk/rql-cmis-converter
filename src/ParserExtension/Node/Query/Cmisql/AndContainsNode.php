<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\Node\Query\Cmisql;

use Xiag\Rql\Parser\Node\Query\AbstractLogicalOperatorNode;

/**
 * Class AndContainsNode.
 */
class AndContainsNode extends AbstractLogicalOperatorNode
{
    /**
     * @return string
     */
    public function getNodeName(): string
    {
        return 'and_contains';
    }
}
