<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\Node;

use Xiag\Rql\Parser\AbstractNode;

/**
 * groupby(field1,field2,...).
 */
class GroupbyNode extends AbstractNode
{
    /**
     * @var array
     */
    private $fields;

    /**
     * GroupbyNode constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getNodeName(): string
    {
        return 'groupby';
    }
}
