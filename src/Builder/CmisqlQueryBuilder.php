<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Builder;

use Tms\Rql\Query\CmisqlQuery;
use Tms\Rql\Query\QueryInterface;

/**
 * Class CmisqlQueryBuilder.
 */
class CmisqlQueryBuilder extends SqlQueryBuilder
{
    /**
     * @return QueryInterface
     */
    protected function getQuery(): QueryInterface
    {
        return new CmisqlQuery($this->selectQuery);
    }
}
