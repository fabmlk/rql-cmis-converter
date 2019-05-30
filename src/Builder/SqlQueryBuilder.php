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
     * SqlQueryBuilder constructor.
     *
     * @param SqlConditionsBuilder $conditionsBuilder
     */
    public function __construct(SqlConditionsBuilder $conditionsBuilder)
    {
        $this->selectQuery = SelectQuery::make();
        $this->conditionsBuilder = $conditionsBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function build(RqlQuery $query, string $table): QueryInterface
    {
        $this->process($query);
        $this->selectQuery->from($table);

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

        foreach ($node->getFields() as $field) {
            if ($field instanceof AggregateNode) {
                $fields[] = e::make(\strtoupper($field->getFunction()).'(%s)', $field->getField());
            } else {
                $fields[] = $field;
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

        // Convert ['a' => 1, 'b' => -1] to [['a', 'ASC'], ['b', 'DESC']]
        $out = [];
        foreach ($node->getFields() as $field => $direction) {
            $out[] = [$field, $direction > 0 ? 'ASC' : 'DESC'];
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
        $this->selectQuery->groupBy(...$node->getFields());
    }

    /**
     * @return SqlQuery
     */
    protected function getQuery(): QueryInterface
    {
        return new SqlQuery($this->selectQuery);
    }
}
