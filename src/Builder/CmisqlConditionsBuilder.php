<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Builder;

use Tms\Rql\ParserExtension\Node\Query\Cmisql\AndContainsNode;
use Tms\Rql\ParserExtension\Node\Query\Cmisql\OrContainsNode;
use Tms\Rql\Visitor\CmisqlSimpleExpressionVisitor;
use Xiag\Rql\Parser\Node\AbstractQueryNode;

/**
 * Class CmisqlConditionsBuilder.
 */
class CmisqlConditionsBuilder extends SqlConditionsBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function visit(AbstractQueryNode $node, ?string $group): void
    {
        if ($node instanceof OrContainsNode || $node instanceof AndContainsNode) {
            $this->applyLogicalGroup($group, 'containsGroup');
        } else {
            parent::visit($node, $group);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getNewGroup(?string $oldgroup, AbstractQueryNode $node): string
    {
        if ($node instanceof OrContainsNode) {
            return 'orGroup';
        }

        if ($node instanceof AndContainsNode) {
            return 'andGroup';
        }

        return parent::getNewGroup($oldgroup, $node);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultExpressionVisitor(): callable
    {
        return new CmisqlSimpleExpressionVisitor();
    }
}
