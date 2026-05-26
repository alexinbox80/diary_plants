<?php

namespace App\Infrastructure\Repository;

use Exception;
use DateTimeImmutable;
use InvalidArgumentException;
use App\Domain\Entity\Incident;
use App\Domain\Model\Incident\IncidentModel;
use App\Domain\Repository\IncidentRepositoryInterface;

class IncidentRepositoryDecorator implements IncidentRepositoryInterface
{
    public function __construct(
        private readonly IncidentRepository $incidentRepository,
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return IncidentModel[]
     * @throws Exception
     */
    public function getIncidentsPaginated(int $page, int $perPage): array
    {
        $incidentsPaginated = $this->incidentRepository->getIncidentsPaginated($page, $perPage);

        if (!is_array($incidentsPaginated['items'])) {
            throw new InvalidArgumentException('Expected array for Incidents');
        }

        $incidentsModel = array_map(
            fn (Incident $incident): IncidentModel => $this->toModel($incident),
            $incidentsPaginated['items']
        );

        return [
            'incidentsModel' => $incidentsModel,
            'pagination' => $incidentsPaginated['pagination']
        ];
    }

    /**
     * @param int $incidentId
     * @return Incident|null
     */
    public function find(int $incidentId): ?Incident
    {
        return $this->incidentRepository->find($incidentId);
    }

    /**
     * @param int $incidentId
     * @return IncidentModel|null
     */
    public function findModel(int $incidentId): ?IncidentModel
    {
        $incident = $this->incidentRepository->find($incidentId);

        return $this->toModel($incident);
    }

    /**
     * @return IncidentModel[]
     */
    public function findAll(): array
    {
        $incidents = $this->incidentRepository->findAll();

        return array_map(
            fn (Incident $incident): IncidentModel => $this->toModel($incident),
            $incidents
        );
    }

    /**
     * @return IncidentModel[]
     */
    public function findAllByUserId(?int $userId = null): array
    {
        $incidents = $this->incidentRepository->findAllByUserId($userId);

        return array_map(
            fn (Incident $incident): IncidentModel => $this->toModel($incident),
            $incidents
        );
    }

    /**
     * @param string $publicCode
     * @return ?IncidentModel
     */
    public function findIncidentByPublicCode(string $publicCode): ?IncidentModel
    {
        $incident = $this->incidentRepository->findIncidentByPublicCode($publicCode);

        return $this->toModel($incident);
    }

    /**
     * @param Incident $incident
     * @return int
     */
    public function create(Incident $incident): int
    {
        return $this->incidentRepository->create($incident);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->incidentRepository->update();
    }

    /**
     * @param Incident $incident
     * @return void
     */
    public function remove(Incident $incident): void
    {
        $this->incidentRepository->remove($incident);
    }

    /**
     * @param Incident $incident
     * @return IncidentModel
     */
    public function toModel(Incident $incident): IncidentModel
    {
        return IncidentModel::fromEntity($incident);
    }

    /**
     * @param DateTimeImmutable $date
     * @return int
     */
    public function deleteOlderThan(DateTimeImmutable $date): int
    {
        return $this->incidentRepository->deleteOlderThan($date);
    }
}
