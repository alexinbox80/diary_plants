<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Usage;
use App\Domain\Model\Usage\UsageModel;
use App\Domain\Repository\UsageRepositoryInterface;
use DateTimeImmutable;

class UsageRepositoryDecorator implements UsageRepositoryInterface
{
    public function __construct(
        private readonly UsageRepository $usageRepository,
    ) {
    }

    /**
     * @return UsageModel[]
     */
    public function findByUsage(string $usableType, int $usableId): array
    {
        $usages = $this->usageRepository->findByUsable($usableType, $usableId);

        return array_map(
            static fn (Usage $usage): UsageModel => new UsageModel(
                $usage->getId(),
                $usage->getUseDate(),
                $usage->getPlant()->getId(),
                $usage->getComment(),
                $usage->getUsableId(),
                $usage->getUsableType(),
                $usage->getCreatedAt(),
                $usage->getUpdatedAt()
            ),
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
            static fn (Usage $usage): UsageModel => new UsageModel(
                $usage->getId(),
                $usage->getUseDate(),
                $usage->getPlant()->getId(),
                $usage->getComment(),
                $usage->getUsableId(),
                $usage->getUsableType(),
                $usage->getCreatedAt(),
                $usage->getUpdatedAt()
            ),
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
        $usages = $this->usageRepository->getUsagesPaginated($page, $perPage);

        return array_map(
            static fn (Usage $usage): UsageModel => new UsageModel(
                $usage->getId(),
                $usage->getUseDate(),
                $usage->getPlant()->getId(),
                $usage->getComment(),
                $usage->getUsableId(),
                $usage->getUsableType(),
                $usage->getCreatedAt(),
                $usage->getUpdatedAt()
            ),
            $usages
        );
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
     * @return UsageModel[]
     */
    public function findAll(): array
    {
        $usages = $this->usageRepository->findAll();

        return array_map(
            static fn (Usage $usage): UsageModel => new UsageModel(
                $usage->getId(),
                $usage->getUseDate(),
                $usage->getPlant()->getId(),
                $usage->getComment(),
                $usage->getUsableId(),
                $usage->getUsableType(),
                $usage->getCreatedAt(),
                $usage->getUpdatedAt()
            ),
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
            static fn (Usage $usage): UsageModel => new UsageModel(
                $usage->getId(),
                $usage->getUseDate(),
                $usage->getPlant()->getId(),
                $usage->getComment(),
                $usage->getUsableId(),
                $usage->getUsableType(),
                $usage->getCreatedAt(),
                $usage->getUpdatedAt()
            ),
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
}
