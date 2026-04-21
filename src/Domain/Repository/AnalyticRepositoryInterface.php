<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Analytic;
use App\Domain\Model\Analytic\AnalyticModel;

interface AnalyticRepositoryInterface
{
    public function getAnalyticsPaginated(int $page, int $perPage): array;
    public function find(int $analyticId): ?Analytic;
    public function findModel(int $analyticId): ?AnalyticModel;
    public function findAll(): array;
    public function findAnalyticsByPlantId(int $plantId): array;
    public function findAnalyticsByGroupId(int $plantId): array;
    public function findOneByPlantId(int $plantId): ?Analytic;
    public function create(Analytic $analytic): int;
    public function update(): void;
    public function remove(Analytic $analytic): void;
    public function toModel(Analytic $analytic): AnalyticModel;
}
