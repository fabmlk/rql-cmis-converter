<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Cmis\Factory;

use Tms\Rql\Cmis\Builder\ConditionsBuilderInterface;
use Xiag\Rql\Parser\Parser;

/**
 * Interface FactoryInterface.
 */
interface FactoryInterface
{
    /**
     * @param string $type
     *
     * @return callable
     */
    public function getExpressionVisitor(string $type = ''): callable;

    /**
     * @return Parser
     */
    public function getParser(): Parser;

    /**
     * @param string $visitorType
     *
     * @return ConditionsBuilderInterface
     */
    public function getConditionsBuilder(string $visitorType = ''): ConditionsBuilderInterface;

    /**
     * @param string $visitorType
     *
     * @return mixed
     */
    public function getBuilder(string $visitorType = '');
}
