<?php

namespace App\Domain\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Usage;
use App\Domain\Model\Usage\UsageModel;
use App\Domain\ValueObject\Enum\Usage\AttachableType;

interface UsageRepositoryInterface
{
    public function findByUsage(string $usableType, int $usableId): array;
    public function findEntitiesByAttachable(AttachableType $attachableType, int $attachableId): object;
    public function findByUsableWithDeleted(string $usableType, int $usableId): array;
    public function findOneByUsable(string $usableType, int $usableId, int $usageId): ?Usage;
    public function deleteByUsable(string $usableType, int $usableId, int $usageId): void;
    public function getUsagesPaginated(int $page, int $perPage): array;
    public function getUsages(int $year, int $month, int $groupId): array;
    public function getUsagesPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array;
    public function findExistingKeys(int $groupId, array $plantIds, array $dates): array;
    public function find(int $usageId): ?Usage;
    public function findModel(int $usageId): ?UsageModel;
    public function findAll(): array;
    public function findAllWithAttachments(?int $groupId = null): array;
    public function findAllWithTargets(): array;
    public function findUsagesByUseDate(DateTimeImmutable $useDate): array;
    public function findBy(int $plantId, string $usableType): array;
    public function findLatestDateBefore(int $groupId, int $usableId, string $usableType, DateTimeImmutable $date): ?DateTimeImmutable;
    public function findPlantIdsByIds(array $ids): array;
    public function create(Usage $usage): int;
    public function update(): void;
    public function remove(Usage $usage): void;
    public function removeByIds(array $ids, int $groupId): int;
    public function toModel(Usage $usage, bool $addRelations = false): UsageModel;
}
