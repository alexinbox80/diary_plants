<?php

namespace App\Domain\Service;

use App\Domain\Entity\Usage;
use App\Domain\Model\Usage\UsageModel;
use App\Domain\Model\Usage\CreateUsageModel;
use App\Domain\Model\Usage\UpdateUsageModel;
use App\Domain\Repository\UsageRepositoryInterface;
use DateTimeImmutable;
use InvalidArgumentException;

class UsageService
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly UsageRepositoryInterface $usageRepository
    ) {
    }

    /**
     * @param int $usageId
     * @return ?Usage
     */
    public function find(int $usageId): ?Usage
    {
        return $this->usageRepository->find($usageId);
    }

    /**
     * @return UsageModel[]
     */
    public function findAll(): array
    {
        return $this->usageRepository->findAll();
    }

    /**
     * @param DateTimeImmutable $useDate
     * @return UsageModel[]
     */
    public function findUsagesByUseDate(DateTimeImmutable $useDate): array
    {
        return $this->usageRepository->findUsagesByUseDate($useDate);
    }

    /**
     * @return UsageModel[]
     * @throws InvalidArgumentException
     */
    public function getUsagesPaginated(int $page, int $perPage): array
    {
        return $this->usageRepository->getUsagesPaginated($page, $perPage);
    }

    /**
     * @param CreateUsageModel $createUsageModel
     * @return UsageModel
     * @throws InvalidArgumentException
     */
    public function create(CreateUsageModel $createUsageModel): UsageModel
    {
        $plant = $this->plantService->find($createUsageModel->plantId);

        $usage = new Usage(
            $createUsageModel->useDate,
            $plant,
            $createUsageModel->comment,
            $createUsageModel->usableId,
            $createUsageModel->usableType
        );

        $this->usageRepository->create($usage);

        return new UsageModel(
            $usage->getId(),
            $usage->getUseDate(),
            $usage->getPlant()->getId(),
            $usage->getComment(),
            $usage->getUsableId(),
            $usage->getUsableType(),
            $usage->getCreatedAt(),
            $usage->getUpdatedAt()
        );
    }

    /**
     * @param Usage $usage
     * @param UpdateUsageModel $updateUsageModel
     * @return UsageModel
     * @throws InvalidArgumentException
     */
    public function update(Usage $usage, UpdateUsageModel $updateUsageModel): UsageModel
    {
        $plant = $this->plantService->find($updateUsageModel->plantId);

        $usage->changeFields(
            $updateUsageModel->useDate,
            $plant,
            $updateUsageModel->comment,
            $updateUsageModel->usableId,
            $updateUsageModel->usableType
        );

        $this->usageRepository->update();

        return new UsageModel(
            $usage->getId(),
            $usage->getUseDate(),
            $usage->getPlant()->getId(),
            $usage->getComment(),
            $usage->getUsableId(),
            $usage->getUsableType(),
            $usage->getCreatedAt(),
            $usage->getUpdatedAt()
        );
    }

    /**
     * @param int $usageId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $usageId): void
    {
        $usage = $this->usageRepository->find($usageId);
        if ($usage !== null) {
            $this->usageRepository->remove($usage);
        }
    }

    /**
     * @param Usage $usage
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeUsage(Usage $usage): void
    {
        $this->usageRepository->remove($usage);
    }
}
