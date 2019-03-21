<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Cmis\Query;

/**
 * Interface QueryInterface.
 */
interface QueryInterface
{
    /**
     * Return the SQL string.
     *
     * @return string
     */
    public function sql(): string;

    /**
     * Return the query params.
     *
     * @return array
     */
    public function params(): array;
}
