<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Builder;

use Latitude\QueryBuilder\Conditions;
use Latitude\QueryBuilder\Expression as e;
use Latitude\QueryBuilder\SelectQuery;
use Tms\Rql\Factory\AliasResolverWrapper;
use Tms\Rql\ParserExtension\Node\GroupbyNode;
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\AggregateNode;
use Tms\Rql\Query\QueryInterface;
use Tms\Rql\Query\SqlQuery;
use Xiag\Rql\Parser\AbstractNode;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\LimitNode;
use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\Node\SortNode;
use Tms\Rql\ParserExtension\SqlQuery as RqlQuery;

/**
 * Class SqlQueryBuilder.
 */
class SqlQueryBuilder implements QueryBuilderInterface
{
    use VisitExpressionDispatcherTrait;

    /**
     * @var SelectQuery
     */
    protected $selectQuery;

    /**
     * @var SqlConditionsBuilder
     */
    protected $conditionsBuilder;

    /**
     * @var AliasResolverWrapper
     */
    protected $aliasResolver;


    /**
     * SqlQueryBuilder constructor.
     *
     * @param SqlConditionsBuilder $conditionsBuilder
     * @param AliasResolverWrapper $aliasResolverWrapper
     */
    public function __construct(SqlConditionsBuilder $conditionsBuilder, AliasResolverWrapper $aliasResolverWrapper)
    {
        $this->conditionsBuilder = $conditionsBuilder;
        $this->aliasResolver = $aliasResolverWrapper;
        $this->selectQuery = SelectQuery::make();
    }

    /**
     * {@inheritdoc}
     *
     * Example:
     *     build($tree, ['foo AS f', 'bar AS b', 'baz AS z']);
     *        => FROM foo AS f, bar AS b, baz AS z
     *     build($tree, ['foo AS f', 'bar AS b', 'baz AS z'], [['', 'f.id = b.id'], ['inner', 'b.id = z.id']]);
     *        => FROM foo AS f JOIN bar AS b ON f.id = b.id INNER JOIN baz AS z ON b.id = z.id
     */
    public function build(RqlQuery $query, $tables, array $joinConditions = []): QueryInterface
    {
        $tables = (array) $tables;

        // our job is to generate valid SQL by default, so if user
        // didn't specify aliases, we create them for her, starting from DEFAULT_ROOT_ALIAS
        foreach ($tables as $i => $table) {
            if (false === strpos(trim($table), ' ')) {
                $tables[$i] .= ' AS '.chr(ord(self::DEFAULT_ROOT_ALIAS) + $i);
            }
        }

        $firstTable = array_shift($tables);

        // set default alias
        $firstTableParts = explode(' ', $firstTable);
        $this->aliasResolver->withAlias(end($firstTableParts));

        $this->process($query);

        foreach ($joinConditions as [$type, $joinCondition]) {
            $joinMethod = \strtolower($type).'Join';
            $this->selectQuery->$joinMethod(array_shift($tables), Conditions::make($joinCondition));
        }

        array_unshift($tables, $firstTable);
        $this->selectQuery->from(...$tables);

        return $this->getQuery();
    }

    /**
     * Process the whole query.
     *
     * @param RqlQuery $query
     */
    protected function process(RqlQuery $query): void
    {
        if (null !== $query->getSelect()) {
            $this->processSelectNode($query->getSelect());
        }
        if (null !== $query->getQuery()) {
            $this->processQueryNode($query->getQuery());
        }
        if (null !== $query->getSort()) {
            $this->processSortNode($query->getSort());
        }
        if (null !== $query->getLimit()) {
            $this->processLimitNode($query->getLimit());
        }
        if (null !== $query->getGroupby()) {
            $this->processGroupbyNode($query->getGroupby());
        }
    }

    /**
     * Process select node.
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

            if ($field instanceof AggregateNode) {
                $fields[] = e::make(\strtoupper($field->getFunction()).'(%s.%s)', $alias, $field->getField());
            } else {
                $fields[] = "$alias.$field";
            }
        }
        $this->selectQuery = $this->selectQuery::make(...$fields);
    }

    /**
     * Process query node.
     *
     * @param AbstractQueryNode $node
     */
    protected function processQueryNode(AbstractQueryNode $node): void
    {
        // notification to listeners will be handled inside the conditions builder
        /** @var VisitExpressionListenerInterface $listener */
        foreach ($this->listeners as $listener) {
            $this->conditionsBuilder->onVisitExpression($listener);
        }

        $conditions = $this->conditionsBuilder->build($node);
        $this->selectQuery->where($conditions);
    }

    /**
     * Process sort node.
     *
     * @param SortNode|AbstractNode $node
     */
    protected function processSortNode(SortNode $node): void
    {
        /** @var SortNode $node */
        $node = $this->notify($node);
        $aliases = (array) ($this->aliasResolver)($node);

        // Convert ['a' => 1, 'b' => -1] to [['o.a', 'ASC'], ['o.b', 'DESC']]
        $out = [];
        $i = 0;
        foreach ($node->getFields() as $field => $direction) {
            $alias = $aliases[$i] ?? $aliases[0];
            $out[] = ["$alias.$field", $direction > 0 ? 'ASC' : 'DESC'];
            $i++;
        }

        $this->selectQuery->orderBy(...$out);
    }

    /**
     * Process limit node.
     *
     * @param LimitNode|AbstractNode $node
     */
    protected function processLimitNode(LimitNode $node): void
    {
        /** @var LimitNode $node */
        $node = $this->notify($node);
        $this->selectQuery->limit($node->getLimit());

        if (null !== $node->getOffset()) {
            $this->selectQuery->offset($node->getOffset());
        }
    }

    /**
     * Process group by node.
     *
     * @param GroupbyNode|AbstractNode $node
     */
    protected function processGroupbyNode(GroupbyNode $node): void
    {
        /** @var GroupbyNode $node */
        $node = $this->notify($node);
        $aliases = (array) ($this->aliasResolver)($node);

        $columns = [];
        foreach ($node->getFields() as $i => $field) {
            $alias = $aliases[$i] ?? $aliases[0];
            $columns[] = "$alias.$field";
        }

        $this->selectQuery->groupBy(...$columns);
    }

    /**
     * @return SqlQuery
     */
    protected function getQuery(): QueryInterface
    {
        return new SqlQuery($this->selectQuery);
    }
}
