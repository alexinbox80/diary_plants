<?php

namespace App\Controller\Web\Dashboard\Incident\GetIncidents;

use InvalidArgumentException;
use App\Domain\Service\IncidentService;
use App\Domain\ValueObject\Enum\Timezone;
use App\Application\Security\AccessContext;
use App\Domain\Model\Incident\IncidentModel;

final class Manager
{
    public function __construct(
        private readonly IncidentService $incidentService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public function getIncidents(): array
    {
        $incidentsModel = $this->incidentService->findAll();

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = IncidentModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (IncidentModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $incidentsModel
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}
