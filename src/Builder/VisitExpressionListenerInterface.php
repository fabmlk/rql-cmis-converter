<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Cmis\Builder;

use Xiag\Rql\Parser\AbstractNode;

/**
 * Interface VisitExpressionListenerInterface.
 */
interface VisitExpressionListenerInterface
{
    /**
     * @param AbstractNode $node reference (meaning the whole node can be swapped if desired)
     */
    public function update(AbstractNode &$node): void;
}
