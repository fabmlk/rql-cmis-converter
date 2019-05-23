<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Builder;

use Tms\Rql\ConditionsExtension\AbstractEnhanceableConditions;
use Tms\Rql\Visitor\SqlSimpleExpressionVisitor;
use Xiag\Rql\Parser\Node\AbstractQueryNode;
use Xiag\Rql\Parser\Node\Query\AbstractLogicalOperatorNode;
use Xiag\Rql\Parser\Node\Query\LogicalOperator\AndNode;
use Xiag\Rql\Parser\Node\Query\LogicalOperator\NotNode;
use Xiag\Rql\Parser\Node\Query\LogicalOperator\OrNode;

/**
 * Class SqlConditionsBuilder.
 */
class SqlConditionsBuilder implements ConditionsBuilderInterface
{
    use VisitExpressionDispatcherTrait;

    /**
     * @var AbstractEnhanceableConditions
     */
    protected $conditions;

    /**
     * @var callable
     */
    protected $expressionVisitor;

    /**
     * SqlConditionsBuilder constructor.
     *
     * @param AbstractEnhanceableConditions $conditions
     * @param callable                      $expressionVisitor
     */
    public function __construct(AbstractEnhanceableConditions $conditions, callable $expressionVisitor = null)
    {
        $this->conditions = $conditions;
        $this->expressionVisitor = $expressionVisitor ?: $this->getDefaultExpressionVisitor();
        $this->listeners = new \SplObjectStorage();
    }

    /**
     * @param AbstractQueryNode $tree
     *
     * @return AbstractEnhanceableConditions
     */
    public function build(AbstractQueryNode $tree): AbstractEnhanceableConditions
    {
        $this->traversePreOrder($tree);

        return $this->conditions;
    }

    /**
     * Algo overview: perform a pre-order tree traversal with:
     *   if current node is a group then
     *      pop stacked-group from the stack if any
     *      if stacked-group found then
     *         execute stacked-group function: 'andGroup' => andGroup(), 'orGroup' => orGroup()...
     *      end
     *      execute current group node matching function: 'AndNode' => andGroup(), 'OrNode' => orGroup() ...
     *      save current group node by pushing its stacked-group function onto the stack
     *   else current node is not a group then
     *       get stacked-group from the stack
     *       execute for current node the with-matching function from stacked-group: 'andGroup' => 'andWith(..)', 'orGroup' => 'orWith(..)'...
     *   end.
     *
     * Note: By making extensive use of groups we end up with extra parentheses but the algorithm is actually simplified.
     *
     * Ex:
     * Given the Query tree:                    ==>                We obtain the following sequence:
     *   and                                                         ->andGroup()       // execute new group
     *    |-- or                                                         ->andGroup()       // takes into account current group
     *    |    |-- eq                                                        ->orGroup()        // execute new group
     *    |    |    |-- a                                                        ->orWith('a = 1')
     *    |    |    |-- "1"                                                      ->orWith('b < 2')
     *    |    |-- lt                                                            ->orGroup()    // takes into account current group
     *    |    |    |-- b                                                            ->andGroup()   // execute new group
     *    |    |    |-- 2                                                                ->andWith('c <> 3')
     *    |    |-- and                                                                   ->andWith('d >= "4")
     *    |    |    |-- ne                                                               ->andWith('e = TRUE')
     *    |    |    |    |-- c                                                       ->end()    // foreach group added, call end()
     *    |    |    |    |-- 3                                                   ->end()
     *    |    |    |-- ge                                                   ->end()
     *    |    |    |    |-- d                                           ->end()
     *    |    |    |    |-- "4"                                         ->andWith('u <> 5')
     *    |    |    |-- eq                                               ->andNotGroup()    // 'NOT' special case: current group + execute new group
     *    |    |         |-- e                                               ->andGroup()       // takes into account current group
     *    |    |         |-- true                                                ->orGroup()        // execute new group
     *    |    |-- ne                                                                ->orWith('u = 6')
     *    |    |    |-- u                                                            ->orWith('i >= 10')
     *    |    |    |-- 5                                                        ->end()
     *    |    |-- not                                                       ->end()
     *    |    |    |-- or                                               ->end()
     *    |    |         |-- eq                                          ->andWith('z = 1')
     *    |    |         |    |-- u                                      ->andGroup()   // takes into account current group
     *    |    |         |    |-- 6                                          ->orGroup()    // execute new group
     *    |    |         |-- ge                                                  ->orWith('a = 2')
     *    |    |              |-- i                                              ->orWith('b < -3')
     *    |    |              |-- 10                                             ->orWith('c IN (2,3.0)')
     *    |    |-- eq                                                        ->end()
     *    |    |    |-- z                                                ->end()
     *    |    |    |-- 1                                            ->end()
     *    |    |-- or
     *    |    |    |-- eq
     *    |    |    |    |-- a
     *    |    |    |    |-- 2
     *    |    |    |-- lt
     *    |    |    |    |-- b
     *    |    |    |    |-- -3
     *    |    |    |-- in
     *    |    |    |    |-- c
     *    |    |    |    |--
     *    |    |    |        |-- 2
     *    |    |    |        |-- 3.0
     *
     * {@link https://packagist.org/packages/xiag/rql-command}
     *
     * @param AbstractQueryNode $node
     * @param null|string       $group
     */
    protected function traversePreOrder(AbstractQueryNode $node, string $group = null): void
    {
        if (null === $node) {
            return;
        }

        $this->visit($node, $group); // pre-order visit

        if ($node instanceof AbstractLogicalOperatorNode) {
            $group = $this->getNewGroup($group, $node);
            foreach ($node->getQueries() as $index => $subnode) { // children nodes recursion
                $this->traversePreOrder($subnode, $group);
            }
            $this->exitLogicalGroups(); // post-order operation
        }
    }

    /**
     * Visit current node to determine which method to call depending on node type.
     *
     * @param AbstractQueryNode $node
     * @param null|string       $group
     *
     * @throws \DomainException if $node is unrecognized
     */
    protected function visit(AbstractQueryNode $node, ?string $group): void
    {
        $this->notify($node); // first, notify the listeners
        
        if ($node instanceof AndNode) {
            $this->applyLogicalGroup($group, 'andGroup');
        } elseif ($node instanceof OrNode) {
            $this->applyLogicalGroup($group, 'orGroup');
        } elseif ($node instanceof NotNode) {
            $prefix = str_replace('Group', '', $group); // 'and' or 'or'
            $this->applyLogicalGroup($prefix.'NotGroup');
        } else {
            // here $node should be an instance of AbstractComparisonOperatorNode
            // $group can be null if the query is a simple expression with no logical operator,
            // in that case we can pick any group we want (andGroup here) as its logical operator
            // name will be stripped off anyway when converting to sql

            $this->applyExpression($group ?: 'andGroup', ($this->expressionVisitor)($node));
        }
    }

    /**
     * Helper to get the current group as a method name from a group (parent) node.
     *
     * @param string|null       $oldgroup
     * @param AbstractQueryNode $node
     *
     * @return string
     *
     * @throws \DomainException if $node is not recognized
     */
    protected function getNewGroup(?string $oldgroup, AbstractQueryNode $node): string
    {
        if ($node instanceof AndNode) {
            return 'andGroup';
        }
        if ($node instanceof OrNode) {
            return 'orGroup';
        }
        if ($node instanceof NotNode) {
            // NotNode is a special custom group node that we will handle differently
            return $oldgroup;
        }

        throw new \DomainException(sprintf('Unknown group node %s', get_class($node)));
    }

    /**
     * Apply logical group.
     *
     * @param string[] ...$groups
     */
    protected function applyLogicalGroup(...$groups): void
    {
        // discard empty groups first
        foreach (array_filter($groups) as $group) {
            $this->conditions = $this->conditions->$group();
        }
    }

    /**
     * Apply leaf expression.
     *
     * @param string $group
     * @param array  $expression
     */
    protected function applyExpression(string $group, array $expression): void
    {
        $with = str_replace('Group', 'With', $group);
        // with() MAY have its 2nd arg "params" depending on the array returned by the visitor
        $this->conditions->$with(...$expression);
    }

    /**
     * Exit logical groups.
     */
    protected function exitLogicalGroups(): void
    {
        // twice as for each group applied we also apply its parent first
        // (except for NOT group but calling more end() than necessary has no effect)
        $this->conditions = $this->conditions->end()->end();
    }

    /**
     * Return default expression visitor.
     *
     * @return callable
     */
    protected function getDefaultExpressionVisitor(): callable
    {
        return new SqlSimpleExpressionVisitor();
    }
}
