<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Cmis\ParserExtension\NodeParser\Query\FunctionOperator\Cmisql;

use Tms\Rql\Cmis\ParserExtension\Node\Query\FunctionOperator\Cmisql\InFolderNode;
use Tms\Rql\Cmis\ParserExtension\NodeParser\Query\FunctionOperator\AbstractUnaryNodeParser;
use Xiag\Rql\Parser\AbstractNode;

/**
 * Class InFolderNodeParser.
 *
 * Parser for infolder()
 */
class InFolderNodeParser extends AbstractUnaryNodeParser
{
    /**
     * {@inheritdoc}
     */
    public function getNodeName(): string
    {
        return 'infolder';
    }

    /**
     * {@inheritdoc}
     */
    public function getNode($value): AbstractNode
    {
        return new InFolderNode($value);
    }
}
