<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\Node\Query\FunctionOperator;

use Xiag\Rql\Parser\Node\AbstractQueryNode;

/**
 * Class AbstractUnaryNode.
 */
abstract class AbstractUnaryNode extends AbstractQueryNode
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * Unary node constructor.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
