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
use Xiag\Rql\Parser\AbstractNode;
use Xiag\Rql\Parser\QueryBuilder as BaseQueryBuilder;

/**
 * Class SqlQueryBuilder.
 */
class SqlQueryBuilder extends BaseQueryBuilder
{
    /**
     * SqlQueryBuilder constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->query = new SqlQuery();
    }

    /**
     * @param AbstractNode $node
     *
     * @return $this|SqlQuery|BaseQueryBuilder
     */
    public function addNode(AbstractNode $node)
    {
        if ($node instanceof GroupbyNode) {
            return $this->query->setGroupby($node);
        }

        return parent::addNode($node);
    }
}
