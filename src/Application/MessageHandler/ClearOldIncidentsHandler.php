<?php

namespace App\Application\MessageHandler;

use App\Application\Message\ClearOldIncidentsMessage;
use App\Domain\Repository\IncidentRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use DateTimeImmutable;

#[AsMessageHandler]
final readonly class ClearOldIncidentsHandler
{
    public function __construct(
        private IncidentRepositoryInterface $incidentRepository
    ) {}

    public function __invoke(ClearOldIncidentsMessage $message): void
    {
        $dateLimit = new DateTimeImmutable('-30 days');
        $this->incidentRepository->deleteOlderThan($dateLimit);
    }
}
