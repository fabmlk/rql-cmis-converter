<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ConditionsExtension;

use Latitude\QueryBuilder\Conditions;
use Tms\Rql\ParserExtension\Node\Query\FunctionOperator\Cmisql\ContainsNode;

/**
 * Class CmisqlContainsConditions.
 */
class CmisqlContainsConditions extends SqlNotConditions
{
    /**
     * @const string
     */
    protected const GROUP_NAME = 'CONTAINS';

    /**
     * @return CmisqlContainsConditions|Conditions
     */
    public function containsGroup(): self
    {
        return $this->addConditionGroup(self::GROUP_NAME);
    }

    /**
     * {@inheritdoc}
     */
    protected function enhanceCondition(array $part): string
    {
        if (self::GROUP_NAME === $part['type']) {
            if (is_callable($this->aliasResolver)) {
                $alias = ($this->aliasResolver)(new ContainsNode());
                return sprintf("%s,'%s'", $alias, parent::enhanceCondition($part));
            }

            return sprintf("'%s'", parent::enhanceCondition($part));
        }

        return parent::enhanceCondition($part);
    }

    /**
     * {@inheritdoc}
     *
     * Add support for 'CONTAINS' group.
     */
    protected function enhanceStatement(array $part, string $statement): string
    {
        if (self::GROUP_NAME === $part['type']) {
            return self::GROUP_NAME.$statement; // statement starts and end with parenthesis as it is a group
        }

        return parent::enhanceStatement($part, $statement);
    }
}
