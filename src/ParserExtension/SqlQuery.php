<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension;

use Tms\Rql\ParserExtension\Node\GroupbyNode;
use Xiag\Rql\Parser\Query as BaseQuery;

/**
 * add supports for "groupby".
 */
class SqlQuery extends BaseQuery
{
    /**
     * @var GroupbyNode
     */
    private $groupby;

    /**
     * @return GroupbyNode
     */
    public function getGroupby(): ?GroupbyNode
    {
        return $this->groupby;
    }

    /**
     * @param GroupbyNode $node
     *
     * @return $this
     */
    public function setGroupby(GroupbyNode $node): self
    {
        $this->groupby = $node;

        return $this;
    }
}
