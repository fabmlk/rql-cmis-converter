<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\ParserExtension\Node\Query\ScalarOperator;

use Xiag\Rql\Parser\Node\Query\AbstractComparisonOperatorNode;

/**
 * between(field,from,to).
 *
 * {@link https://github.com/xiag-ag/rql-parser/blob/master/examples/02-new-query-operator.php}
 */
class BetweenNode extends AbstractComparisonOperatorNode
{
    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * BetweenNode constructor.
     *
     * @param string $field
     * @param string $from
     * @param string $to
     */
    public function __construct(string $field, int $from, int $to)
    {
        $this->field = $field;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getFrom(): int
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getTo(): int
    {
        return $this->to;
    }

    /**
     * @return string
     */
    public function getNodeName(): string
    {
        return 'between';
    }
}
