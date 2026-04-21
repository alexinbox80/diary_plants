<?php

namespace App\Domain\EventSubscriber;

use App\Domain\Event\UsageIsCreatedEvent;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UsageEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            UsageIsCreatedEvent::class => 'onUsageIsCreated',
        ];
    }

    public function onUsageIsCreated(UsageIsCreatedEvent $event): void
    {
        if (AttachableType::isWatering($event->usableType)) {
            //dd($event);
        }

    }
}
