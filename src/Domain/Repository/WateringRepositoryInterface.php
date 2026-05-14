<?php

namespace App\Domain\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Watering;
use App\Domain\Model\Watering\WateringModel;

interface WateringRepositoryInterface
{
    public function getWateringsForDairy(int $groupId): array;
    public function getWateringsPaginated(int $page, int $perPage): array;
    public function getWateringsPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array;
    public function find(int $wateringId): ?Watering;
    public function findModel(int $wateringId): ?WateringModel;
    public function findAll(): array;
    public function findAllByGroupId(?int $groupId = null): array;
    public function findWateringsByType(string $type): array;
    public function findWateringsByMethod(string $method): array;
    public function findWateringsByWateredAt(DateTimeImmutable $wateredAt): array;
    public function create(Watering $watering): int;
    public function update(): void;
    public function remove(Watering $watering): void;
    public function toModel(Watering $watering, bool $addRelations = false): WateringModel;
}
