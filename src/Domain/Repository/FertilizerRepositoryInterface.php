<?php

namespace App\Domain\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Fertilizer;
use App\Domain\Model\Fertilizer\FertilizerModel;

interface FertilizerRepositoryInterface
{
    public function getFertilizersPaginated(int $page, int $perPage): array;
    public function find(int $fertilizerId): ?Fertilizer;
    public function findModel(int $fertilizerId): ?FertilizerModel;
    public function findAll(): array;
    public function findFertilizersByTitle(string $title): array;
    public function findFertilizersByManufacturer(string $manufacturer): array;
    public function findFertilizersByUseDate(DateTimeImmutable $date): array;
    public function create(Fertilizer $fertilizer): int;
    public function update(): void;
    public function remove(Fertilizer $fertilizer): void;
    public function toModel(Fertilizer $fertilizer, bool $addRelations = false): FertilizerModel;
}
