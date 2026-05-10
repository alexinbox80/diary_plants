<?php

namespace App\Infrastructure\Repository;

use Exception;
use DateTimeImmutable;
use App\Domain\Entity\Usage;
use InvalidArgumentException;
use App\Domain\Model\Usage\UsageModel;
use App\Domain\Repository\PestRepositoryInterface;
use App\Domain\Repository\UsageRepositoryInterface;
use App\Domain\Entity\Interfaces\AttachableInterface;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use App\Domain\Repository\AttachableResolverInterface;
use App\Domain\Repository\WateringRepositoryInterface;
use App\Domain\Repository\StimulantRepositoryInterface;
use App\Domain\Repository\FertilizerRepositoryInterface;
use App\Domain\Model\Interfaces\AttachableModelInterface;

class UsageRepositoryDecorator implements UsageRepositoryInterface
{
    public function __construct(
        private readonly GroupRepositoryDecorator      $groupRepository,
        private readonly PlantRepositoryDecorator      $plantRepository,
        private readonly UsageRepository               $usageRepository,
        private readonly AttachableResolverInterface   $attachableResolver,
        private readonly FertilizerRepositoryInterface $fertilizerRepository,
        private readonly PestRepositoryInterface       $pestRepository,
        private readonly StimulantRepositoryInterface  $stimulantRepository,
        private readonly WateringRepositoryInterface   $wateringRepository,
    ) {
    }

    /**
     * @param string $usableType
     * @param int $usableId
     * @return UsageModel[]
     */
    public function findByUsage(string $usableType, int $usableId): array
    {
        $usages = $this->usageRepository->findByUsable($usableType, $usableId);

        return array_map(
            fn (Usage $usage) => $this->toModel($usage),
            $usages
        );
    }

    /**
     * @param AttachableType $attachableType
     * @param int $attachableId
     * @return object
     */
    public function findEntitiesByAttachable(AttachableType $attachableType, int $attachableId): object
    {
        return $this->attachableResolver->resolve(
            $attachableType,
            $attachableId
        );
    }

    /**
     * @param string $usableType
     * @param int $usableId
     * @return UsageModel[]
     */
    public function findByUsableWithDeleted(string $usableType, int $usableId): array
    {
        $usages = $this->usageRepository->findByUsableWithDeleted($usableType, $usableId);

        return array_map(
            fn (Usage $usage) => $this->toModel($usage),
            $usages
        );
    }

    /**
     * @param string $usableType
     * @param int $usableId
     * @param int $usageId
     * @return Usage|null
     */
    public function findOneByUsable(string $usableType, int $usableId, int $usageId): ?Usage
    {
        return $this->usageRepository->findOneByUsable($usableType, $usableId, $usageId);
    }

    /**
     * @param string $usableType
     * @param int $usableId
     * @param int $usageId
     * @return void
     */
    public function deleteByUsable(string $usableType, int $usableId, int $usageId): void
    {
        $this->usageRepository->deleteByUsable($usableType, $usableId, $usageId);
    }

    /**
     * @param int $plantId
     * @param string $usableType
     * @return UsageModel[]
     */
    public function findBy(int $plantId, string $usableType): array
    {
        $criteria = [
                'plant' => $plantId,
                'target.usableType' => $usableType
            ];

        $orderBy = ['useDate' => 'ASC'];

        return array_map(
            fn (Usage $usage): UsageModel => $this->toModel($usage),
            $this->usageRepository->findBy($criteria, $orderBy)
        );
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return UsageModel[]
     * @throws Exception
     */
    public function getUsagesPaginated(int $page, int $perPage): array
    {
        $usagesPaginated = $this->usageRepository->getUsagesPaginated($page, $perPage);

        if (!is_array($usagesPaginated['items'])) {
            throw new InvalidArgumentException('Expected array for usages');
        }

        $usagesModel = array_map(
            fn (Usage $usage): UsageModel => $this->toModel($usage, true),
            $usagesPaginated['items']
        );

        return [
            'usagesModel' => $usagesModel,
            'pagination' => $usagesPaginated['pagination']
        ];
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $groupId
     * @return UsageModel[]
     * @throws Exception
     */
    public function getUsages(int $year, int $month, int $groupId): array
    {
        $usages = $this->usageRepository->getUsages($year, $month, $groupId);

        return array_map(
            fn (Usage $usage): UsageModel => $this->toModel($usage, true),
            $usages
        );
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param int|null $groupId
     * @return array{usagesModel: usageModel[], pagination: array}
     * @throws Exception
    */
    public function getUsagesPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array
    {
        $usagesPaginated = $this->usageRepository->getUsagesPaginatedByGroupIdWithAttachments($page, $perPage, $groupId);

        if (!is_array($usagesPaginated['items'])) {
            throw new InvalidArgumentException('Expected array for usages');
        }

        $usagesModel = array_map(
            fn (Usage $usage): UsageModel => $this->toModel($usage, true),
            $usagesPaginated['items']
        );

        return [
            'usagesModel' => $usagesModel,
            'pagination' => $usagesPaginated['pagination']
        ];
    }

    /**
     * @param int $groupId
     * @param array $plantIds
     * @param array $dates
     * @return Usage[]
     */
    public function findExistingKeys(int $groupId, array $plantIds, array $dates): array
    {
        $usages = $this->usageRepository->findExistingKeys($groupId, $plantIds, $dates);

        return array_map(function($row) {
            return sprintf(
                '%s_%s_%s_%s_%s',
                $row['groupId'],
                $row['plantId'],
                $row['useDate']->format('Y-m-d'),
                $row['usableId'],
                $row['usableType']->value
            );
        }, $usages);
    }

    /**
     * @param int $usageId
     * @return Usage|null
     */
    public function find(int $usageId): ?Usage
    {
        return $this->usageRepository->find($usageId);
    }

    /**
     * @param int $usageId
     * @return UsageModel|null
     */
    public function findModel(int $usageId): ?UsageModel
    {
        $usage = $this->usageRepository->find($usageId);

        return $this->toModel($usage);
    }

    /**
     * @return UsageModel[]
     */
    public function findAll(): array
    {
        $usages = $this->usageRepository->findAll();

        return array_map(
            fn (Usage $usage): UsageModel => $this->toModel($usage, true),
            $usages
        );
    }

    /**
     * @param null|int $groupId
     * @return UsageModel[]
     */
    public function findAllWithAttachments(?int $groupId = null): array
    {
        $usages = $this->usageRepository->findAllWithAttachments($groupId);

        return array_map(
            fn (Usage $usage): UsageModel => $this->toModel($usage, true),
            $usages
        );
    }

    /**
     * @return UsageModel[]
     */
    public function findAllWithTargets(): array
    {
        $usages = $this->usageRepository->findAllWithTargets();

        return array_map(
            fn (Usage $usage): UsageModel => $this->toModel($usage, true),
            $usages
        );
    }

    /**
     * @param DateTimeImmutable $useDate
     * @return UsageModel[]
     */
    public function findUsagesByUseDate(DateTimeImmutable $useDate): array
    {
        $usages = $this->usageRepository->findUsagesByUseDate($useDate);

        return array_map(
            fn (Usage $usage) => $this->toModel($usage),
            $usages
        );
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
     * @param array $ids
     * @return array
     */
    public function findPlantIdsByIds(array $ids): array
    {
        return $this->usageRepository->findPlantIdsByIds($ids);
    }

    /**
     * @param Usage $usage
     * @return int
     */
    public function create(Usage $usage): int
    {
        return $this->usageRepository->create($usage);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->usageRepository->update();
    }

    /**
     * @param Usage $usage
     * @return void
     */
    public function remove(Usage $usage): void
    {
        $this->usageRepository->remove($usage);
    }

    /**
     * @param array $ids
     * @param int $groupId
     * @return int
     */
    public function removeByIds(array $ids, int $groupId): int
    {
        return $this->usageRepository->removeByIds($ids, $groupId);
    }

    /**
     * @param Usage $usage
     * @param bool $addRelations
     * @return UsageModel
     */
    public function toModel(Usage $usage, bool $addRelations = false): UsageModel
    {
        $groupModel = $this->groupRepository->toModel($usage->getGroup());
        $plantModel = $this->plantRepository->toModel($usage->getPlant());

        $attachableModel = null;

        if ($addRelations) {
            $attachableEntity = $this->attachableResolver->resolve(
                $usage->getTarget()->getUsableType(),
                $usage->getTarget()->getUsableId()
            );

            $attachableModel = $this->toAttachableModel($attachableEntity);
        }

        return UsageModel::fromEntity($usage, $groupModel, $plantModel, $attachableModel);
    }

    /**
     * @param AttachableInterface|null $entity
     * @return AttachableModelInterface|null
     */
    private function toAttachableModel(?AttachableInterface $entity): ?AttachableModelInterface
    {
        if (!$entity) {
            return null;
        }

        $className = get_class($entity);

        return match (AttachableType::fromClass($className)) {
            AttachableType::FERTILIZER => $this->fertilizerRepository->toModel($entity, true),
            AttachableType::PEST => $this->pestRepository->toModel($entity, true),
            AttachableType::STIMULANT => $this->stimulantRepository->toModel($entity, true),
            AttachableType::WATERING => $this->wateringRepository->toModel($entity, true),
            null => null,
        };
    }
}
