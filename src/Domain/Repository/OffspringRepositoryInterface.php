<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Offspring;
use App\Domain\Model\Offspring\OffspringModel;

interface OffspringRepositoryInterface
{
    public function getOffspringsPaginated(int $page, int $perPage): array;
    public function getOffspringsPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array;
    public function find(int $offspringId): ?Offspring;
    public function findModel(int $offspringId): ?OffspringModel;
    public function findAll(): array;
    public function findAllWithAttachments(?int $groupId = null): array;
    public function findOffspringsByMass(string $mass): array;
    public function findOffspringsByFlavor(string $flavor): array;
    public function findOffspringsByColor(string $color): array;
    public function create(Offspring $offspring): int;
    public function update(): void;
    public function remove(Offspring $offspring): void;
    public function toModel(Offspring $offspring, bool $addRelations = false): OffspringModel;
}
