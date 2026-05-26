<?php

namespace App\Domain\Service;

use Throwable;
use DateTimeImmutable;
use App\Domain\Entity\Incident;
use App\Domain\ValueObject\RequestDetails;
use Symfony\Bundle\SecurityBundle\Security;
use App\Domain\Model\Incident\IncidentModel;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Model\Incident\CreateIncidentModel;
use App\Domain\Model\Incident\UpdateIncidentModel;
use App\Domain\Repository\IncidentRepositoryInterface;
use App\Controller\Web\Dashboard\Incident\EditIncident\Input\EditIncidentDTO;
use App\Controller\Web\Dashboard\Incident\CreateIncident\Input\CreateIncidentDTO;

class IncidentService
{
    public function __construct(
        private readonly IncidentRepositoryInterface $incidentRepository,
        private readonly Security $security
    ) {
    }

    /**
     * @param int $incidentId
     * @return ?Incident
     */
    public function find(int $incidentId): ?Incident
    {
        return $this->incidentRepository->find($incidentId);
    }

    /**
     * @return Incident[]
     */
    public function findAll(): array
    {
        return $this->incidentRepository->findAll();
    }

    /**
     * @param int|null $userId
     * @return IncidentModel[]
     */
    public function findAllByUserId(?int $userId = null): array
    {
        return $this->incidentRepository->findAllByUserId($userId);
    }

    /**
     * @param string $publicCode
     * @return ?IncidentModel
     */
    public function findIncidentByTitle(string $publicCode): ?IncidentModel
    {
        return $this->incidentRepository->findIncidentByPublicCode($publicCode);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return IncidentModel[]
     */
    public function getIncidentsPaginated(int $page, int $perPage): array
    {
        return $this->incidentRepository->getIncidentsPaginated($page, $perPage);
    }

    public function createFromException(Throwable $exception, Request $request, int $statusCode): string
    {
        $requestDetails = new RequestDetails(
            uri: $request->getUri(),
            method: $request->getMethod(),
            clientIp: $request->getClientIp(),
            payload: $request->getContent() ?: null
        );

        $user = $this->security->getUser();
        $userId = $user instanceof EntityInterface ? $user->getId() : null;

        $incident = new Incident(
            requestDetails: $requestDetails,
            statusCode: $statusCode,
            errorMessage: $exception->getMessage(),
            stackTrace: explode("\n", $exception->getTraceAsString()),
            userId: $userId
        );

        try {
            $this->incidentRepository->create($incident);

            return $incident->getPublicCode();
        } catch (Throwable) {
            // Фолбэк на случай сбоя самой БД
            return 'ERR-DATABASE-DOWN';
        }
    }

    /**
     * @param CreateIncidentModel $createIncidentModel
     * @return IncidentModel
     */
    public function create(CreateIncidentModel $createIncidentModel): IncidentModel
    {
        $incident = null;

        $this->incidentRepository->create($incident);

        return $this->incidentRepository->toModel($incident);
    }

    /**
     * @param Incident $incident
     * @param UpdateIncidentModel $updateIncidentModel
     * @return IncidentModel
     */
    public function update(Incident $incident, UpdateIncidentModel $updateIncidentModel): IncidentModel
    {
        $this->incidentRepository->update();

        return $this->incidentRepository->toModel($incident);
    }

    /**
     * @param int $incidentId
     * @return void
     */
    public function removeById(int $incidentId): void
    {
        $incident = $this->incidentRepository->find($incidentId);
        if ($incident !== null) {
            $this->incidentRepository->remove($incident);
        }
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
