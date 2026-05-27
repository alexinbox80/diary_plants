<?php

namespace App\Application\MessageHandler;

use DateTimeImmutable;
use App\Application\Message\ClearOldIncidentsMessage;
use App\Domain\Repository\IncidentRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ClearOldIncidentsHandler
{
    public function __construct(
        private IncidentRepositoryInterface $incidentRepository
    ) {}

    public function __invoke(ClearOldIncidentsMessage $message): void
    {
        $dateLimit = new DateTimeImmutable(sprintf('-%d days', $message->daysToKeep));
        $this->incidentRepository->deleteOlderThan($dateLimit);
    }
}
