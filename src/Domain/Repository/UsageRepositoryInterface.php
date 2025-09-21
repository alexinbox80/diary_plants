<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Usage;
use App\Domain\Model\Usage\UsageModel;
use DateTime;

interface UsageRepositoryInterface
{
    public function findByUsage(string $usableType, int $usableId): array;
    public function findByUsableWithDeleted(string $usableType, int $usableId): array;
    public function findOneByUsable(string $usableType, int $usableId, int $usageId): ?Usage;
    public function deleteByUsable(string $usableType, int $usableId, int $usageId): void;
    public function getUsagesPaginated(int $page, int $perPage): array;
    public function find(int $usageId): ?Usage;
    public function findModel(int $usageId): ?UsageModel;
    public function findAll(): array;
    public function findUsagesByUseDate(DateTime $useDate): array;
    public function create(Usage $usage): int;
    public function update(): void;
    public function remove(Usage $usage): void;
}
