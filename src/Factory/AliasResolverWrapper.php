<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Factory;

use Xiag\Rql\Parser\AbstractNode;

/**
 * Class AliasResolverWrapper.
 */
class AliasResolverWrapper
{
    /**
     * @var string
     */
    private $defaultAlias;

    /**
     * @var callable
     */
    private $publicAliasResolver;

    /**
     * AliasResolverWrapper constructor.
     *
     * @param callable $publicAliasResolver
     */
    public function __construct(callable $publicAliasResolver)
    {
        $this->publicAliasResolver = $publicAliasResolver;
    }

    /**
     * Utilisé par SqlQueryBuilder après avoir identifié le default alias
     *
     * @param string $alias
     *
     * @return $this
     */
    public function withAlias(string $alias): self
    {
        $this->defaultAlias = $alias;

        return $this;
    }

    /**
     * @param AbstractNode $node
     */
    public function __invoke(AbstractNode $node)
    {
        return ($this->publicAliasResolver)($node, $this->defaultAlias);
    }
}