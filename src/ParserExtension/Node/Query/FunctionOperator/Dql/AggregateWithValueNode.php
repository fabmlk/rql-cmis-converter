<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Dql;

use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\AggregateNode;

/**
 * aggregate(fieldName,value).
 * Exemple: identity(foo,'foo')
 */
class AggregateWithValueNode extends AggregateNode
{
    /**
     * @var string|null
     */
    private $value;

    /**
     * AggregateWithValueNode constructor.
     *
     * @param string $function
     * @param string $field
     * @param mixed|null $value
     */
    public function __construct(string $function, string $field, $value = null)
    {
        parent::__construct($function, $field);
        $this->value = $value;
    }

    /**
     * @return mixed|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if ($this->value) {
            return sprintf('%s(%s,%s)', $this->getFunction(), $this->getField(), $this->value);
        }
        return parent::__toString();
    }
}
