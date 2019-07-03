<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Dql;

use Xiag\Rql\Parser\Node\AbstractQueryNode;

/**
 * Exemple: atdepth(1234,5)
 */
abstract class AbstractFunctionWithTwoValuesNode extends AbstractQueryNode
{
    /**
     * @var string
     */
    private $function;

    /**
     * @var mixed
     */
    private $left;

    /**
     * @var mixed
     */
    private $right;

    /**
     * FunctionWithTwoValuesNode constructor.
     *
     * @param string $function
     * @param mixed $left
     * @param mixed $right
     */
    public function __construct(string $function, $left, $right)
    {
        $this->function = $function;
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * @return string
     */
    public function getFunction(): string
    {
        return $this->getNodeName();
    }

    /**
     * @return mixed
     */
    public function getLeftValue()
    {
        return $this->left;
    }

    /**
     * @return mixed
     */
    public function getRightValue()
    {
        return $this->right;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s(%s,%s)', $this->getFunction(), $this->getLeftValue(), $this->getRightValue());
    }
}
