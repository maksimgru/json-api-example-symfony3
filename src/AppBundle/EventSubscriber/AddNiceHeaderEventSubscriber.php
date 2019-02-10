<?php

namespace AppBundle\EventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class AddNiceHeaderEventSubscriber
 *
 * @package AppBundle\EventSubscriber
 */
class AddNiceHeaderEventSubscriber implements EventSubscriberInterface
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $event->getResponse()
            ->headers->set('X-NICE-MESSAGE', 'That was a great request!');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse'
        ];
    }
}
