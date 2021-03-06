<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Dql;

/**
 * Class AtDepthNode.
 */
class AtDepthNode extends AbstractFunctionWithTwoValuesNode
{
    /**
     * {@inheritDoc}
     */
    public function getNodeName()
    {
        return 'atdepth';
    }
}
