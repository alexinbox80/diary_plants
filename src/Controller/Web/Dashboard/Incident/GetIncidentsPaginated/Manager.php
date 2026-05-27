<?php

namespace App\Controller\Web\Dashboard\Incident\GetIncidentsPaginated;

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
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws InvalidArgumentException
     */
    public function getIncidentsPaginated(int $page, int $perPage): array
    {
        $incidentsModel = $this->incidentService->getIncidentsPaginated($page, $perPage);

        $timezone = $this->accessContext->getTimezone();
        $tableHeader = IncidentModel::getTableHeaderRu();

        $tableBody = array_map(
            static fn (IncidentModel $model): array => $model->toArray(Timezone::tryFrom($timezone)),
            $incidentsModel['incidentsModel']
        );

        return [
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody,
            'pagination' => $incidentsModel['pagination'],
        ];
    }
}
