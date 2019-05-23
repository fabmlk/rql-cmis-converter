<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Visitor;

use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Cmisql\AftsNode;
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Cmisql\CoeNode;
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Cmisql\ColNode;
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Cmisql\InFolderNode;
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Cmisql\InTreeNode;
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Cmisql\NceNode;
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Cmisql\NclNode;
use Tms\Rql\ParserExtension\Node\Query\ScalarOperator\Cmisql\InAnyNode;
use Tms\Rql\ParserExtension\Node\Query\ScalarOperator\Cmisql\OutAnyNode;
use Tms\Rql\ParserExtension\Node\Query\ScalarOperator\Cmisql\EqAnyNode;
use Xiag\Rql\Parser\Glob;
use Xiag\Rql\Parser\Node\AbstractQueryNode;

/**
 * Class CmisqlSimpleExpressionVisitor.
 */
class CmisqlSimpleExpressionVisitor extends SqlSimpleExpressionVisitor
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(AbstractQueryNode $node): array
    {
        switch (true) {
            case $node instanceof EqAnyNode:
                return [
                    sprintf('%s = ANY %s', $this->encodeValue($node->getValue()), $node->getField()),
                ];
            case $node instanceof InAnyNode:
                return [
                    sprintf('ANY %s IN %s', $node->getField(), $this->encodeValue($node->getValues())),
                ];
            case $node instanceof OutAnyNode:
                return [
                    sprintf('ANY %s NOT IN %s', $node->getField(), $this->encodeValue($node->getValues())),
                ];
            case $node instanceof NceNode:
                return [
                    sprintf('-%s', $this->encodeContainsValue($node->getValue())),
                ];
            case $node instanceof CoeNode:
            case $node instanceof ColNode:
            case $node instanceof NclNode:
                return [
                    $this->encodeContainsValue($node->getValue()),
                ];
            case $node instanceof AftsNode:
                return [
                    sprintf('%s:%s', $node->getField(), sprintf('"%s"', $node->getValue()))
                ];
            case $node instanceof InTreeNode:
                return [
                    sprintf('IN_TREE(%s)', $this->encodeValue($node->getValue()))
                ];
            case $node instanceof InFolderNode:
                return [
                    sprintf('IN_FOLDER(%s)', $this->encodeValue($node->getValue()))
                ];
            default:
                return parent::__invoke($node);
        }
    }

    /**
     * @param string|Glob $value
     *
     * @return string
     */
    protected function encodeContainsValue($value): string
    {
        if (\is_string($value)) {
            return sprintf('"%s"', \addslashes($value));
        }

        if ($value instanceof Glob) {
            return \addslashes($value->__toString());
        }

        throw new \LogicException(sprintf('Invalid value "%s"', var_export($value, true)));
    }
}
