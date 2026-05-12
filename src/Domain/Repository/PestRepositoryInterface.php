<?php

namespace App\Domain\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Pest;
use App\Domain\Model\Pest\PestModel;

interface PestRepositoryInterface
{
    public function getPestsForDairy(int $groupId): array;
    public function getPestsPaginated(int $page, int $perPage): array;
    public function getPestsPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array;
    public function find(int $pestId): ?Pest;
    public function findModel(int $pestId): ?PestModel;
    public function findAll(): array;
    public function findAllByGroupId(?int $groupId = null): array;
    public function findPestsByTitle(string $title): array;
    public function findPestsByManufacturer(string $manufacturer): array;
    public function findPestsByUseDate(DateTimeImmutable $date): array;
    public function create(Pest $pest): int;
    public function update(): void;
    public function remove(Pest $pest): void;
    public function toModel(Pest $pest, bool $addRelations = false): PestModel;
}
