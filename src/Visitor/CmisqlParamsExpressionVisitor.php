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
use Tms\Rql\ParserExtension\Node\Query\ScalarOperator\Cmisql\EqAnyNode;
use Tms\Rql\ParserExtension\Node\Query\ScalarOperator\Cmisql\InAnyNode;
use Tms\Rql\ParserExtension\Node\Query\ScalarOperator\Cmisql\OutAnyNode;
use Xiag\Rql\Parser\Glob;
use Xiag\Rql\Parser\Node\AbstractQueryNode;

/**
 * Class CmisqlParamsExpressionVisitor.
 */
class CmisqlParamsExpressionVisitor extends SqlParamsExpressionVisitor
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(AbstractQueryNode $node): array
    {
        switch (true) {
            case $node instanceof EqAnyNode:
                $alias = ($this->aliasResolver)($node);
                return [
                    sprintf('? = ANY %s.%s', $alias, $node->getField()),
                    $this->encodeValue($node->getValue()),
                ];
            case $node instanceof InAnyNode:
                $alias = ($this->aliasResolver)($node);
                return [
                    sprintf('ANY %s.%s IN ?', $alias, $node->getField()),
                    $this->encodeValue($node->getValues()),
                ];
            case $node instanceof OutAnyNode:
                $alias = ($this->aliasResolver)($node);
                return [
                    sprintf('ANY %s.%s NOT IN ?', $alias, $node->getField()),
                    $this->encodeValue($node->getValues()),
                ];
            case $node instanceof ColNode:
            case $node instanceof NclNode:
            case $node instanceof CoeNode:
                return [
                    '?', $this->encodeContainsValue($node->getValue()),
                ];
            case $node instanceof NceNode:
                return [
                    '-?', $this->encodeContainsValue($node->getValue()),
                ];
            case $node instanceof AftsNode:
                return [
                    sprintf('%s:?', $node->getField()),
                    sprintf('"%s"', $node->getValue())
                ];
            case $node instanceof InTreeNode:
                $alias = ($this->aliasResolver)($node);
                return [
                    sprintf('IN_TREE(%s,?)', $alias),
                    $this->encodeValue($node->getValue())
                ];
            case $node instanceof InFolderNode:
                $alias = ($this->aliasResolver)($node);
                return [
                    sprintf('IN_FOLDER(%s,?)', $alias),
                    $this->encodeValue($node->getValue())
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

    /**
     * {@inheritDoc}
     */
    protected function encodeValue($value): string
    {
        if ($value instanceof \DateTime) {
            // from CMIS standard http://docs.oasis-open.org/cmis/CMIS/v1.1/CMIS-v1.1.html#x1-1110001
            return 'TIMESTAMP '.var_export($value->format(\DateTime::RFC3339_EXTENDED), true);
        }

        return parent::encodeValue($value);
    }
}
