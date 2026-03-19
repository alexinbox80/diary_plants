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
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use App\Controller\Web\Dashboard\Usage\EditUsage\Input\EditUsageDTO;
use App\Controller\Web\Dashboard\Usage\CreateUsage\Input\CreateUsageDTO;

class UsageService
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly GroupService $groupService,
        private readonly UsageRepositoryInterface $usageRepository,
        private readonly ModelFactory $modelFactory,
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
        return $this->usageRepository->findAllWithTargets();
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
                AttachableType::tryFrom($createUsageModel->usableType)
            ),
            $createUsageModel->comment
        );

        $this->usageRepository->create($usage);

        return $this->usageRepository->toModel($usage);
    }

    /**
     * @param CreateUsageDTO $dto
     * @return UsageModel
     */
    public function createFromCreateUsageDTO(CreateUsageDTO $dto): UsageModel
    {
        $model = $this->modelFactory->makeModel(
            CreateUsageModel::class,
            2,
            $dto->plantId,
            $dto->useDate,
            $dto->usableId,
            $dto->usableType,
            $dto->comment
        );

        return $this->create($model);
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
                AttachableType::tryFrom($updateUsageModel->usableType)
            ),
            $updateUsageModel->comment
        );

        $this->usageRepository->update();

        return $this->usageRepository->toModel($usage);
    }

    /**
     * @param Usage $usage
     * @param EditUsageDTO $dto
     */
    public function updateFromEditUsageDTO(Usage $usage, EditUsageDTO $dto): void
    {
        $model = $this->modelFactory->makeModel(
            UpdateUsageModel::class,
            2,
            $dto->plantId,
            $dto->useDate,
            $dto->usableId,
            $dto->usableType,
            $dto->comment
        );

        $this->update($usage, $model);
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
