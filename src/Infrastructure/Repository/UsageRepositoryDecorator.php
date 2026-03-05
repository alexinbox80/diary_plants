<?php

namespace App\Infrastructure\Repository;

use App\Domain\Model\Group\GroupModel;
use DateTimeImmutable;
use App\Domain\Entity\Usage;
use App\Domain\Model\Usage\UsageModel;
use App\Domain\Repository\PestRepositoryInterface;
use App\Domain\Repository\UsageRepositoryInterface;
use App\Domain\Entity\Interfaces\AttachableInterface;
use App\Domain\Repository\AttachableResolverInterface;
use App\Domain\Repository\StimulantRepositoryInterface;
use App\Domain\Repository\FertilizerRepositoryInterface;
use App\Domain\Model\Interfaces\AttachableModelInterface;

class UsageRepositoryDecorator implements UsageRepositoryInterface
{
    public function __construct(
        private readonly GroupRepositoryDecorator $groupRepository,
        private readonly UsageRepository $usageRepository,
        private readonly AttachableResolverInterface $attachableResolver,
        private readonly FertilizerRepositoryInterface $fertilizerRepository,
        private readonly PestRepositoryInterface $pestRepository,
        private readonly StimulantRepositoryInterface $stimulantRepository,
    ) {
    }

    /**
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

    public function findOneByUsable(string $usableType, int $usableId, int $usageId): ?Usage
    {
        return $this->usageRepository->findOneByUsable($usableType, $usableId, $usageId);
    }

    public function deleteByUsable(string $usableType, int $usableId, int $usageId): void
    {
        $this->usageRepository->deleteByUsable($usableType, $usableId, $usageId);
    }

    /**
     * @return UsageModel[]
     */
    public function getUsagesPaginated(int $page, int $perPage): array
    {
        $usagesPaginated = $this->usageRepository->getUsagesPaginated($page, $perPage);

        if (!is_array($usagesPaginated['items'])) {
            throw new \InvalidArgumentException('Expected array for usages');
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
     * @param Usage $usage
     * @param bool $addRelations
     * @return UsageModel
     */
    public function toModel(Usage $usage, bool $addRelations = false): UsageModel
    {
        $groupModel = $this->groupRepository->findModel($usage->getGroup()->getId());

        $attachableModel = null;

        if ($addRelations) {
            $attachableEntity = $this->attachableResolver->resolve(
                $usage->getTarget()->getUsableType(),
                $usage->getTarget()->getUsableId()
            );

            $attachableModel = $this->toAttachableModel($attachableEntity);
        }

        return self::makeUsageModel($usage, $groupModel, $attachableModel);
    }

    /**
     * @param AttachableInterface|null $entity
     * @return AttachableModelInterface|null
     */
    private function toAttachableModel(?AttachableInterface $entity): ?AttachableModelInterface
    {
        if (!$entity) return null;

        return match (get_class($entity)) {
            \App\Domain\Entity\Fertilizer::class => $this->fertilizerRepository->findModel($entity->getId()),
            \App\Domain\Entity\Pest::class => $this->pestRepository->findModel($entity->getId()),
            \App\Domain\Entity\Stimulant::class => $this->stimulantRepository->findModel($entity->getId()),
            default => null,
        };
    }

    /**
     * @param Usage $usage
     * @param GroupModel|null $groupModel
     * @param AttachableModelInterface|null $attachableModel
     * @return UsageModel
     */
    static function makeUsageModel(Usage $usage, ?GroupModel $groupModel = null, ?AttachableModelInterface $attachableModel = null): UsageModel
    {
        return new UsageModel(
            $usage->getId(),
            $usage->getGroup()->getId(),
            $groupModel,
            $usage->getUseDate(),
            $usage->getPlant()->getId(),
            $usage->getComment(),
            $usage->getTarget()->getUsableId(),
            $usage->getTarget()->getUsableType(),
            $attachableModel,
            $usage->getCreatedAt(),
            $usage->getUpdatedAt()
        );
    }
}
