<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\NodeParser\Query\FunctionOperator\Cmisql;

use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Cmisql\ColNode;
use Tms\Rql\ParserExtension\NodeParser\Query\FunctionOperator\AbstractUnaryNodeParser;
use Xiag\Rql\Parser\AbstractNode;

/**
 * Class ColNodeParser.
 *
 * Parser for col() (stands for 'co'ntains 'l'ike)
 */
class ColNodeParser extends AbstractUnaryNodeParser
{
    /**
     * {@inheritdoc}
     */
    public function getNodeName(): string
    {
        return 'col';
    }

    /**
     * {@inheritdoc}
     */
    public function getNode($value): AbstractNode
    {
        return new ColNode($value);
    }
}
