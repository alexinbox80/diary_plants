<?php

namespace App\Domain\Service;

use DateTimeImmutable;
use App\Domain\Entity\Usage;
use InvalidArgumentException;
use App\Domain\Model\Usage\UsageModel;
use App\Domain\Model\Usage\CreateUsageModel;
use App\Domain\Model\Usage\UpdateUsageModel;
use App\Domain\Repository\UsageRepositoryInterface;
use App\Domain\ValueObject\Usage\AttachableReference;

class UsageService
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly GroupService $groupService,
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
        $group = $this->groupService->find($createUsageModel->groupId);
        $plant = $this->plantService->find($createUsageModel->plantId);

        $usage = new Usage(
            $group,
            $createUsageModel->useDate,
            $plant,
            new AttachableReference(
                $createUsageModel->usableId,
                $createUsageModel->usableType
            ),
            $createUsageModel->comment
        );

        $this->usageRepository->create($usage);

        return $this->usageRepository->toModel($usage);
    }

    /**
     * @param Usage $usage
     * @param UpdateUsageModel $updateUsageModel
     * @return UsageModel
     * @throws InvalidArgumentException
     */
    public function update(Usage $usage, UpdateUsageModel $updateUsageModel): UsageModel
    {
        $group = $this->groupService->find($updateUsageModel->groupId);
        $plant = $this->plantService->find($updateUsageModel->plantId);

        $usage->changeFields(
            $group,
            $updateUsageModel->useDate,
            $plant,
            new AttachableReference(
                $updateUsageModel->usableId,
                $updateUsageModel->usableType
            ),
            $updateUsageModel->comment
        );

        $this->usageRepository->update();

        return $this->usageRepository->toModel($usage);
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
