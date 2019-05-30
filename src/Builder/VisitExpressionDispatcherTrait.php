<?php
/**
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Rql\Builder;

use Xiag\Rql\Parser\AbstractNode;

/**
 * Trait VisitExpressionDispatcherTrait.
 */
trait VisitExpressionDispatcherTrait
{
    /**
     * @var  \SplObjectStorage
     */
    private $listeners;

    /**
     * Attach a VisitExpressionListenerInterface $listener.
     *
     * @param VisitExpressionListenerInterface $listener
     */
    public function onVisitExpression(VisitExpressionListenerInterface $listener): void
    {
        if (null === $this->listeners) {
            $this->listeners = new \SplObjectStorage();
        }
        $this->listeners->attach($listener);
    }

    /**
     * Notify an observer.
     *
     * @param AbstractNode $node
     */
    public function notify(AbstractNode $node): void
    {
        foreach ($this->listeners as $listener) {
            $listener->update($node);
        }
    }
}
