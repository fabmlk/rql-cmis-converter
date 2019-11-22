<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Builder;

use Latitude\QueryBuilder\Expression as e;
use Latitude\QueryBuilder\SelectQuery;
use Tms\Rql\Factory\AliasResolverWrapper;
use Tms\Rql\ParserExtension\Node\GroupbyNode;
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Dql\AggregateWithValueNode;
use Tms\Rql\Query\DqlQuery;
use Tms\Rql\Query\QueryInterface;
use Xiag\Rql\Parser\AbstractNode;
use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\Node\SortNode;
use Tms\Rql\ParserExtension\SqlQuery as RqlQuery;

/**
 * Class DqlQueryBuilder.
 */
class DqlQueryBuilder extends SqlQueryBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function process(RqlQuery $query): void
    {
        if (null === $query->getSelect()) {
            $this->selectQuery = $this->selectQuery::make($this->aliasResolver->getDefaultAlias());
        }
        parent::process($query);
    }

    /**
     * {@inheritdoc}
     *
     * @param SelectNode|AbstractNode $node
     */
    protected function processSelectNode(SelectNode $node): void
    {
        $fields = [];

        /** @var SelectNode $node */
        $node = $this->notify($node);
        $aliases = (array) ($this->aliasResolver)($node);

        foreach ($node->getFields() as $i => $field) {
            $alias = $aliases[$i] ?? $aliases[0];

            if ($field instanceof AggregateWithValueNode) {
                if ($field->getValue()) {
                    $fields[] = e::make(\strtoupper($field->getFunction()).'(%s.%s,%s)', $alias, $field->getField(), $field->getValue());
                } else {
                    $fields[] = e::make(\strtoupper($field->getFunction()).'(%s.%s)', $alias, $field->getField());
                }
            } else {
                $fields[] = "$alias.$field";
            }
        }

        $this->selectQuery = $this->selectQuery::make(...$fields);
    }

    /**
     * @return DqlQuery
     */
    protected function getQuery(): QueryInterface
    {
        return new DqlQuery($this->selectQuery);
    }
}
