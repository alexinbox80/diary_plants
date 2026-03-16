<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Plant;
use App\Domain\ValueObject\Price;
use App\Domain\Model\Plant\PlantModel;

interface PlantRepositoryInterface
{
    public function getPlantsPaginated(int $page, int $perPage): array;
    public function getPlantsCount(): int;
    public function getPlantsForForm(?int $groupId = null): array;
    public function find(int $plantId): ?Plant;
    public function findModel(int $plantId): ?PlantModel;
    public function findAll(): array;
    public function findAllWithAttachments(): array;
    public function findPlantsByTitle(string $title): array;
    public function findPlantsByPrice(Price $price): array;
    public function create(Plant $plant): int;
    public function update(): void;
    public function remove(Plant $plant): void;
    public function toModel(Plant $plant, bool $addRelations = false): PlantModel;
}
