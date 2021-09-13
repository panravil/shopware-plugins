<?php

declare(strict_types=1);

namespace Change\OrderStatus\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\System\StateMachine\Event\StateMachineTransitionEvent;

class FrontendSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            StateMachineTransitionEvent::class => 'stateChanged'
        ];
    }

    public function stateChanged(StateMachineTransitionEvent $event): void
    {
        
    }
}
