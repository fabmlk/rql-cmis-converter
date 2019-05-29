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
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Dql\AggregateWithValueNode;
use Tms\Rql\Query\DqlQuery;
use Tms\Rql\Query\QueryInterface;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\LimitNode;
use Xiag\Rql\Parser\Node\SelectNode;
use Xiag\Rql\Parser\Node\SortNode;
use Xiag\Rql\Parser\Query as RqlQuery;

/**
 * Class DqlQueryBuilder.
 */
class DqlQueryBuilder extends SqlQueryBuilder
{
    /**
     * @var string
     */
    private $rootAlias;

    /**
     * SqlQueryBuilder constructor.
     *
     * @param ConditionsBuilderInterface $conditionsBuilder
     * @param string                     $rootAlias the entity alias involved in the construction of the query
     */
    public function __construct(ConditionsBuilderInterface $conditionsBuilder, string $rootAlias)
    {
        parent::__construct($conditionsBuilder);
        $this->selectQuery = SelectQuery::make($rootAlias);
        $this->rootAlias = $rootAlias;
    }

    /**
     * {@inheritdoc}
     */
    public function build(RqlQuery $query, string $entity): QueryInterface
    {
        $this->process($query);
        $this->selectQuery->from($entity.' AS '.$this->rootAlias);

        return $this->getQuery();
    }

    /**
     * Process select node.
     *
     * @param SelectNode $node
     */
    protected function processSelectNode(SelectNode $node): void
    {
        $fields = [];

        $this->notify($node);

        foreach ($node->getFields() as $field) {
            if ($field instanceof AggregateWithValueNode) {
                if ($field->getValue()) {
                    $fields[] = e::make(\strtoupper($field->getFunction()).'(%s,%s)', $this->wrapWithRootAlias($field->getField()), $field->getValue());
                } else {
                    $fields[] = e::make(\strtoupper($field->getFunction()).'(%s)', $this->wrapWithRootAlias($field->getField()));
                }
            } else {
                $fields[] = $this->wrapWithRootAlias($field);
            }
        }
        $this->selectQuery = $this->selectQuery::make(...$fields);
    }

    /**
     * Process sort node.
     *
     * @param SortNode $node
     */
    protected function processSortNode(SortNode $node): void
    {
        $this->notify($node);

        // Convert ['a' => 1, 'b' => -1] to [['o.a', 'ASC'], ['o.b', 'DESC']]
        $out = [];
        foreach ($node->getFields() as $field => $direction) {
            $out[] = [$this->wrapWithRootAlias($field), $direction > 0 ? 'ASC' : 'DESC'];
        }

        $this->selectQuery->orderBy(...$out);
    }

    /**
     * Process group by node.
     *
     * @param GroupbyNode $node
     */
    protected function processGroupbyNode(GroupbyNode $node): void
    {
        $this->notify($node);
        $this->selectQuery->groupBy(...array_map([$this, 'wrapWithRootAlias'], $node->getFields()));
    }

    /**
     * @return DqlQuery
     */
    protected function getQuery(): QueryInterface
    {
        return new DqlQuery($this->selectQuery);
    }

    /**
     * @param string $field
     *
     * @return string
     */
    private function wrapWithRootAlias(string $field): string
    {
        return $this->rootAlias.'.'.$field;
    }
}
