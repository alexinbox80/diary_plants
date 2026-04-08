<?php

namespace App\Infrastructure\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Repotting;
use App\Domain\Model\Repotting\RepottingModel;
use App\Domain\Repository\RepottingRepositoryInterface;

class RepottingRepositoryDecorator implements RepottingRepositoryInterface
{
    public function __construct(
        private readonly GroupRepositoryDecorator $groupRepository,
        private readonly PlantRepositoryDecorator $plantRepository,
        private readonly RepottingRepository $repottingRepository
    ) {
    }

    /**
     * @return RepottingModel[]
     * @throws \Exception
     */
    public function getRepottingsPaginated(int $page, int $perPage): array
    {
        $repottingsPaginated = $this->repottingRepository->getRepottingsPaginated($page, $perPage);

        if (!is_array($repottingsPaginated['items'])) {
            throw new \InvalidArgumentException('Expected array for Repottings');
        }

        $repottingsModel = array_map(
            fn (Repotting $repotting): RepottingModel => $this->toModel($repotting, true),
            $repottingsPaginated['items']
        );

        return [
            'repottingsModel' => $repottingsModel,
            'pagination' => $repottingsPaginated['pagination']
        ];
    }

    /**
     * @param int $repottingId
     * @return Repotting|null
     */
    public function find(int $repottingId): ?Repotting
    {
        return $this->repottingRepository->find($repottingId);
    }

    /**
     * @param int $repottingId
     * @return RepottingModel|null
     */
    public function findModel(int $repottingId): ?RepottingModel
    {
        $repotting = $this->repottingRepository->find($repottingId);

        return $this->toModel($repotting);
    }

    /**
     * @return RepottingModel[]
     */
    public function findAll(): array
    {
        $repottings = $this->repottingRepository->findAll();

        return array_map(
            fn (Repotting $repotting): RepottingModel => $this->toModel($repotting, true),
            $repottings
        );
    }

    /**
     * @param DateTimeImmutable $repottedAt
     * @return RepottingModel[]
     */
    public function findRepottingsByRepottedAt(DateTimeImmutable $repottedAt): array
    {
        $repottings = $this->repottingRepository->findRepottingsByRepottedAt($repottedAt);

        return array_map(
            fn (Repotting $repotting): RepottingModel => $this->toModel($repotting),
            $repottings
        );
    }

    /**
     * @param Repotting $repotting
     * @return int
     */
    public function create(Repotting $repotting): int
    {
        return $this->repottingRepository->create($repotting);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->repottingRepository->update();
    }

    /**
     * @param Repotting $repotting
     * @return void
     */
    public function remove(Repotting $repotting): void
    {
        $this->repottingRepository->remove($repotting);
    }

    /**
     * @param Repotting $repotting
     * @param bool $addRelations
     * @return RepottingModel
     */
    public function toModel(Repotting $repotting, bool $addRelations = false): RepottingModel
    {
        $groupModel = null;
        $plantModel = null;

        if ($addRelations) {
            $groupModel = $this->groupRepository->toModel($repotting->getGroup());
            $plantModel = $this->plantRepository->toModel($repotting->getPlant());
        }

        return RepottingModel::fromEntity($repotting, $groupModel, $plantModel);
    }
}
