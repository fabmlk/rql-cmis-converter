<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Builder;


use Latitude\QueryBuilder\ValueList;

/**
 * Class DqlIndexedPlaceholderValueList.
 */
class DqlIndexedPlaceholderValueList extends ValueList
{
    /**
     * @var int
     */
    private $offsetStart;

    /**
     * DqlIndexedPlaceholderValueList constructor.
     *
     * @param int $offsetStart
     */
    public function __construct(int $offsetStart)
    {
        $this->offsetStart = $offsetStart;
    }

    /**
     * Create a new value list for dql indexed-placeholders.
     *
     * @param array $params
     * @param int   $offsetStart
     *
     * @return ValueList
     */
    public static function make(array $params, int $offsetStart = null): ValueList
    {
        $values = new static($offsetStart);
        $values->params = array_values($params);
        return $values;
    }

    /**
     * {@inheritDoc}
     */
    protected function placeholderValue(int $index): string
    {
        $value = $this->params[$index];

        if ($this->isPlaceholderValue($value)) {
            return '?' . ($this->offsetStart + $index);
        }

        return parent::placeholderValue($index);
    }
}