<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Stimulant;
use App\Domain\Model\Stimulant\StimulantModel;
use DateTime;

interface StimulantRepositoryInterface
{
    public function getStimulantsPaginated(int $page, int $perPage): array;
    public function find(int $stimulantId): ?Stimulant;
    public function findModel(int $stimulantId): ?StimulantModel;
    public function findAll(): array;
    public function findStimulantsByTitle(string $title): array;
    public function findStimulantsByManufacturer(string $manufacturer): array;
    public function findStimulantsByUseDate(DateTime $date): array;
    public function create(Stimulant $stimulant): int;
    public function update(): void;
    public function remove(Stimulant $stimulant): void;
}
