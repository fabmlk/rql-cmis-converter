<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\NodeParser\Query\FunctionOperator\Dql;

use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Dql\AtDepthNode;
use Xiag\Rql\Parser\AbstractNode;

/**
 * Class AtDepthWithDepthNodeParser.
 */
class AtDepthWithDepthNodeParser extends AbstractFunctionWithTwoValuesNodeParser
{
    /**
     * {@inheritDoc}
     */
    public function getNodeName(): string
    {
        return 'atdepth';
    }

    /**
     * {@inheritDoc}
     */
    public function getNode($left, $right): AbstractNode
    {
        return new AtDepthNode($this->getNodeName(), $left, $right);
    }
}