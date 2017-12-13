<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\NodeParser\Query\FunctionOperator\Cmisql;

use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Cmisql\NclNode;
use Tms\Rql\ParserExtension\NodeParser\Query\FunctionOperator\AbstractUnaryNodeParser;
use Xiag\Rql\Parser\AbstractNode;

/**
 * Class ColNodeParser.
 *
 * Parser for nce() (stands for 'n'ot 'c'ntains 'l'ike)
 */
class NclNodeParser extends AbstractUnaryNodeParser
{
    /**
     * {@inheritdoc}
     */
    public function getNodeName(): string
    {
        return 'ncl';
    }

    /**
     * {@inheritdoc}
     */
    public function getNode($value): AbstractNode
    {
        return new NclNode($value);
    }
}
