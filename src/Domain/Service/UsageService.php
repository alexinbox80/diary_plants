<?php

namespace App\Domain\Service;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Usage;
use InvalidArgumentException;
use App\Domain\Model\Usage\UsageModel;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Event\UsageIsCreatedEvent;
use App\Domain\Event\UsagesBulkDeletedEvent;
use App\Domain\Model\Usage\CreateUsageModel;
use App\Domain\Model\Usage\UpdateUsageModel;
use App\Domain\Repository\UsageRepositoryInterface;
use App\Domain\ValueObject\Usage\AttachableReference;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Controller\Web\Dashboard\Usage\EditUsage\Input\EditUsageDTO;
use App\Controller\Web\Dashboard\Usage\CreateUsage\Input\CreateUsageDTO;

class UsageService
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly GroupService $groupService,
        private readonly UsageRepositoryInterface $usageRepository,
        private readonly ModelFactory $modelFactory,
        private readonly EventDispatcherInterface $eventDispatcher,
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
     * @param PlantModel $plantModel
     * @param string $usableType
     * @return UsageModel[]
     */
    public function findBy(PlantModel $plantModel, string $usableType): array
    {
        return $this->usageRepository->findBy($plantModel->getId(), $usableType);
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
     * @param int $groupId
     * @param int $usableId
     * @param string $usableType
     * @param DateTimeImmutable $date
     * @return DateTimeImmutable|null
     */
    public function findLatestDateBefore(int $groupId, int $usableId, string $usableType, DateTimeImmutable $date): ?DateTimeImmutable
    {
        return $this->usageRepository->findLatestDateBefore($groupId, $usableId, $usableType, $date);
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
     * @param ?int $year
     * @param ?int $month
     * @return UsageModel[]
     */
    public function getUsages(?int $year = null, ?int $month = null): array
    {
        $timezone = new DateTimeZone('Europe/Moscow');
        $date = new DateTimeImmutable()->setTimezone($timezone);

        if ($month === null) {
            $month = $date->format('n');
        }

        if ($year === null) {
            $year = $date->format('Y');
        }

        $usagesModel = $this->usageRepository->getUsages($year, $month, 2);

        return array_map(
            static fn (UsageModel $model): array => $model->toJson(),
            $usagesModel
        );
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

        $model = $this->usageRepository->toModel($usage);

        $event = new UsageIsCreatedEvent(
            $model->getId(),
            $model->getGroupId(),
            $model->getUseDate(),
            $model->getPlantId(),
            $model->getUsableId(),
            $model->getUsableType()
        );

        $this->eventDispatcher->dispatch($event);

        return $model;
    }

    /**
     * @param array $createUsagesDTO
     * @return null|array
     * @throws \DateMalformedStringException
     */
    public function createUsages(array $createUsagesDTO): ?array
    {
        $groupId = 2;

        // 1. Собираем все ID растений и даты для фильтрации
        $plantIds = array_unique(array_map(fn($dto) => $dto->plantId, $createUsagesDTO));
        $dates = array_unique(array_map(fn($dto) => $dto->date, $createUsagesDTO));

        // 2. Получаем ключи уже существующих в БД записей одним запросом
        $existingKeys = $this->usageRepository->findExistingKeys($groupId, $plantIds, $dates);

        $results = [];
        foreach ($createUsagesDTO as $createUsageDTO) {
            $currentKey = sprintf(
                '%s_%s_%s_%s_%s',
                $groupId,
                $createUsageDTO->plantId,
                $createUsageDTO->date,
                $createUsageDTO->usableId,
                $createUsageDTO->usableType
            );

            $date = new DateTimeImmutable($createUsageDTO->date);

            // Проверяем, нет ли уже такой записи
            if (in_array($currentKey, $existingKeys, true)) {
                continue;
            }

            $model = $this->modelFactory->makeModel(
                CreateUsageModel::class,
                $groupId, // groupId
                $createUsageDTO->plantId,
                $date,
                $createUsageDTO->usableId,
                $createUsageDTO->usableType,
                null
            );

            $newModel = $this->create($model);

            $timezone = new DateTimeZone('Europe/Moscow');
            $day = (int) $newModel->getUseDate()->setTimezone($timezone)->format('d');

            $results[] = [
                'cellId' => $newModel->getUsableType() . '-' . $newModel->getPlant()->getId() * 100 + $day, // "watering-4510"
                'baseId' => $newModel->getId()
            ];

            if (!$results) {
                return null;
            }
        }

        return $results;
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

    /**
     * @param array $ids
     * @return int
     */
    public function removeUsages(array $ids): int
    {
        $plantIds = $this->usageRepository->findPlantIdsByIds($ids);

        $count = $this->usageRepository->removeByIds($ids, 2);

        if ($count > 0 && !empty($plantIds)) {
            $this->eventDispatcher->dispatch(
                new UsagesBulkDeletedEvent($plantIds, AttachableType::WATERING->value)
            );
        }

        return $count;
    }
}
