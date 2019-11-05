<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Builder;

use Tms\Rql\Query\QueryInterface;
use Tms\Rql\ParserExtension\SqlQuery as RqlQuery;

/**
 * Interface QueryBuilderInterface.
 */
interface QueryBuilderInterface
{
    /**
     * @const string
     */
    public const DEFAULT_ROOT_ALIAS = 'o';

    /**
     * @param RqlQuery        $query
     * @param string|string[] $tables
     * @param array           $joinConditions [<join type> => 'join condition']
     *
     * @return QueryInterface
     */
    public function build(RqlQuery $query, $tables, array $joinConditions = []): QueryInterface;
}
