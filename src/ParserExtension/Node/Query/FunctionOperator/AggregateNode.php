<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\Node\Query\FunctionOperator;

use Xiag\Rql\Parser\AbstractNode;

/**
 * aggregate(fieldName).
 *
 * {@link https://github.com/xiag-ag/rql-parser/blob/master/examples/03-new-top-operator.php}
 */
class AggregateNode extends AbstractNode
{
    /**
     * @var string
     */
    private $function;

    /**
     * @var string
     */
    private $field;

    /**
     * AggregateNode constructor.
     *
     * @param string $function
     * @param string $field
     */
    public function __construct(string $function, string $field)
    {
        $this->function = $function;
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getFunction(): string
    {
        return $this->function;
    }

    /**
     * @return string
     */
    public function getNodeName(): string
    {
        return $this->function;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s(%s)', $this->function, $this->field);
    }
}
