<?php

namespace App\Domain\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Repotting;
use App\Domain\Model\Repotting\RepottingModel;

interface RepottingRepositoryInterface
{
    public function getRepottingsPaginated(int $page, int $perPage): array;
    public function getRepottingsPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array;
    public function find(int $repottingId): ?Repotting;
    public function findModel(int $repottingId): ?RepottingModel;
    public function findAll(): array;
    public function findAllByGroupId(?int $groupId = null): array;
    public function findRepottingsByRepottedAt(DateTimeImmutable $repottedAt): array;
    public function create(Repotting $repotting): int;
    public function update(): void;
    public function remove(Repotting $repotting): void;
    public function toModel(Repotting $repotting, bool $addRelations = false): RepottingModel;
}
