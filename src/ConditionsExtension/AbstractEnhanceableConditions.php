<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Cmis\ConditionsExtension;

use Latitude\QueryBuilder\Conditions;

/**
 * Class EnhanceableConditions.
 */
abstract class AbstractEnhanceableConditions extends Conditions
{
    /**
     * Enhances a statement.
     *
     * @param array  $part
     * @param string $statement
     *
     * @return string
     */
    abstract protected function enhanceStatement(array $part, string $statement): string;

    /**
     * Enhances a condition.
     *
     * @param array $part
     *
     * @return string
     */
    protected function enhanceCondition(array $part): string
    {
        return $part['condition']->sql();
    }

    /**
     * {@inheritdoc}
     *
     * Add support for enhanceable statement.
     */
    protected function sqlReducer(): callable
    {
        return function (string $sql, array $part): string {
            if ($this->isCondition($part['condition'])) {
                // (...)
                $statement = '('.$this->enhanceCondition($part).')';
            } else {
                // foo = ?
                $statement = $this->replaceStatementParams($part['condition'], $part['params']);
            }
            if ($sql) {
                $statement = "{$part['type']} $statement";
            } else {
                $statement = $this->enhanceStatement($part, $statement);
            }

            return \trim($sql.' '.$statement);
        };
    }
}
