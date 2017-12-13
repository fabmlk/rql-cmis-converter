<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Query;

use Latitude\QueryBuilder\SelectQuery;
use Tms\Rql\Identifier\CmisqlIdentifier;

/**
 * Class CmisqlQuery.
 */
class CmisqlQuery implements QueryInterface
{
    /**
     * @var SelectQuery
     */
    protected $selectQuery;

    /**
     * SqlQuery constructor.
     *
     * @param SelectQuery $selectQuery
     */
    public function __construct(SelectQuery $selectQuery)
    {
        $this->selectQuery = $selectQuery;
    }

    /**
     * {@inheritdoc}
     */
    public function sql(): string
    {
        return $this->selectQuery->sql(CmisqlIdentifier::make());
    }

    /**
     * {@inheritdoc}
     */
    public function params(): array
    {
        return $this->selectQuery->params();
    }
}
