<?php

namespace App\Infrastructure\Scheduler;

use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\RecurringMessage;
use App\Application\Message\ClearOldIncidentsMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule]
final class AppScheduleProvider implements ScheduleProviderInterface
{
    public function __construct(
        private readonly int $incidentsDaysToKeep,
    ) {}

    public function getSchedule(): Schedule
    {
        return (new Schedule())->with(
        // Каждые сутки отправляем в Messenger задачу на очистку
            RecurringMessage::every('1 day', new ClearOldIncidentsMessage($this->incidentsDaysToKeep))
        );
    }
}
