<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Incident;
use App\Domain\Model\Incident\IncidentModel;

interface IncidentRepositoryInterface
{
    public function getIncidentsPaginated(int $page, int $perPage): array;
    public function find(int $incidentId): ?Incident;
    public function findModel(int $incidentId): ?IncidentModel;
    public function findAll(): array;
    public function findAllByUserId(?int $userId = null): array;
    public function findIncidentByPublicCode(string $publicCode): ?IncidentModel;
    public function create(Incident $incident): int;
    public function update(): void;
    public function remove(Incident $incident): void;
    public function toModel(Incident $incident): IncidentModel;
}
