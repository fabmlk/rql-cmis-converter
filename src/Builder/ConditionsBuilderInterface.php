<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Builder;

use Tms\Rql\ConditionsExtension\AbstractEnhanceableConditions;
use Xiag\Rql\Parser\Node\AbstractQueryNode;

/**
 * Interface ConditionsBuilderInterface.
 */
interface ConditionsBuilderInterface
{
    /**
     * @param AbstractQueryNode $tree
     *
     * @return AbstractEnhanceableConditions
     */
    public function build(AbstractQueryNode $tree): AbstractEnhanceableConditions;
}
