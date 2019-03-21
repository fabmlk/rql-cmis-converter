<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Cmis\Builder;

use Tms\Rql\Cmis\Query\QueryInterface;
use Xiag\Rql\Parser\Query as RqlQuery;

/**
 * Interface QueryBuilderInterface.
 */
interface QueryBuilderInterface
{
    /**
     * @param RqlQuery $query
     * @param string   $table
     *
     * @return QueryInterface
     */
    public function build(RqlQuery $query, string $table): QueryInterface;
}
