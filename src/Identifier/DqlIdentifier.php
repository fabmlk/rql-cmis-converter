<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Identifier;

use Latitude\QueryBuilder\Identifier;
use Latitude\QueryBuilder\IdentifierException;

/**
 * Class DqlIdentifier.
 */
class DqlIdentifier extends Identifier
{
    /**
     * {@inheritdoc}
     *
     * I wish I could override guardIdentifier() but we cant't: it is final !
     * Instead we override it's caller to bypass this restriction.
     */
    public function escape(string $identifier): string
    {
        if ('*' === $identifier) {
            return $identifier;
        }

        $this->guardIdentifierOverride($identifier);

        return $this->surround($identifier);
    }

    /**
     * Ensure that identifiers match DQL identifier or fully_qualified_name:
     * https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/dql-doctrine-query-language.html#ebnf
     *
     * @param string $identifier
     *
     * @throws IdentifierException if the identifier is not valid
     */
    public function guardIdentifierOverride(string $identifier): void
    {
        if (false === \preg_match('/^([a-z_][a-z0-9_]*\\\\?)+(?<!\\\\)$/', $identifier)) {
            throw IdentifierException::invalidIdentifier($identifier);
        }
    }
}
