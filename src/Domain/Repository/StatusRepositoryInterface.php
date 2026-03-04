<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Status;
use App\Domain\Model\Status\StatusModel;

interface StatusRepositoryInterface
{
    public function getStatusesPaginated(int $page, int $perPage): array;
    public function find(int $statusId): ?Status;
    public function findModel(int $statusId): ?StatusModel;
    public function findAll(): array;
    public function findStatusesByLetter(string $letter): array;
    public function findStatusesByColor(string $color): array;
    public function create(Status $status): int;
    public function update(): void;
    public function remove(Status $status): void;
    public function toModel(Status $status, bool $addRelations = false): StatusModel;
}
