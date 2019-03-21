<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Cmis\ConditionsExtension;

/**
 * Class EnhanceableConditions.
 */
class SqlNotConditions extends AbstractEnhanceableConditions
{
    /**
     * Latitude's Conditions does not handle NOT as groups.
     * In the case where NOT is in the other side of a boolean expression, it has to be followed by AND or OR
     * in order to be syntactically correct. As Latitude's Conditions does not accept adjacent group operators
     * (trimmed in its sql() method), we have to prepend them explicitly when creating the group.
     * For that reason, we provide andNotGroup() and orNotGroup() also.
     *
     * Start a new grouping that will be applied with a logical "NOT" (currently unused).
     *
     * @return AbstractEnhanceableConditions
     */
    public function notGroup(): self
    {
        return $this->addConditionGroup('NOT');
    }

    /**
     * Start a new grouping that will be applied with a logical "AND NOT".
     *
     * @return SqlNotConditions
     */
    public function andNotGroup(): self
    {
        return $this->addConditionGroup('AND NOT');
    }

    /**
     * Start a new grouping that will be applied with a logical "OR NOT".
     *
     * @return SqlNotConditions
     */
    public function orNotGroup(): self
    {
        return $this->addConditionGroup('OR NOT');
    }

    /**
     * {@inheritdoc}
     *
     * Add support for 'NOT' group when at the beginning of a statement.
     */
    protected function enhanceStatement(array $part, string $statement): string
    {
        if (false !== strpos($part['type'], 'NOT')) {
            // Here we received AND NOT / OR NOT but it actually starts a new statement
            return "NOT $statement";
        }

        return $statement;
    }
}
