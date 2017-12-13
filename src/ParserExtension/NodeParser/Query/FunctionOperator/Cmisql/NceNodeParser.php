<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\NodeParser\Query\FunctionOperator\Cmisql;

use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Cmisql\NceNode;
use Tms\Rql\ParserExtension\NodeParser\Query\FunctionOperator\AbstractUnaryNodeParser;
use Xiag\Rql\Parser\AbstractNode;

/**
 * Class ColNodeParser.
 *
 * Parser for nce() (stands for 'n'ot 'c'ntains 'e'xactly)
 */
class NceNodeParser extends AbstractUnaryNodeParser
{
    /**
     * {@inheritdoc}
     */
    public function getNodeName(): string
    {
        return 'nce';
    }

    /**
     * {@inheritdoc}
     */
    public function getNode($value): AbstractNode
    {
        return new NceNode((string) $value);
    }
}
