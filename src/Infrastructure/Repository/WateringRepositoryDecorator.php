<?php

namespace App\Infrastructure\Repository;

use Exception;
use DateTimeImmutable;
use InvalidArgumentException;
use App\Domain\Entity\Watering;
use App\Domain\Model\Watering\WateringModel;
use App\Domain\Repository\WateringRepositoryInterface;

class WateringRepositoryDecorator implements WateringRepositoryInterface
{
    public function __construct(
        private readonly GroupRepositoryDecorator $groupRepository,
        private readonly MarkerRepositoryDecorator $markerRepository,
        private readonly WateringRepository $wateringRepository
    ) {
    }

    /**
     * @param int $groupId
     * @return WateringModel[]
     */
    public function getWateringsForDairy(int $groupId): array
    {
        $waterings = $this->wateringRepository->getWateringsForDairy($groupId);

        return array_map(
            fn (Watering $watering): WateringModel => $this->toModel($watering, true),
            $waterings
        );
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return WateringModel[]
     * @throws Exception
     */
    public function getWateringsPaginated(int $page, int $perPage): array
    {
        $wateringsPaginated = $this->wateringRepository->getWateringsPaginated($page, $perPage);

        if (!is_array($wateringsPaginated['items'])) {
            throw new \InvalidArgumentException('Expected array for fertilizers');
        }

        $wateringsModel = array_map(
            fn (Watering $watering): WateringModel => $this->toModel($watering, true),
            $wateringsPaginated['items']
        );

        return [
            'wateringsModel' => $wateringsModel,
            'pagination' => $wateringsPaginated['pagination']
        ];
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param int|null $groupId
     * @return WateringModel[]
     * @throws Exception
     */
    public function getWateringsPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array
    {
        $wateringsPaginated = $this->wateringRepository->getWateringsPaginatedByGroupId($page, $perPage, $groupId);

        if (!is_array($wateringsPaginated['items'])) {
            throw new InvalidArgumentException('Expected array for Waterings');
        }

        $wateringsModel = array_map(
            fn (Watering $watering): WateringModel => $this->toModel($watering, true),
            $wateringsPaginated['items']
        );

        return [
            'wateringsModel' => $wateringsModel,
            'pagination' => $wateringsPaginated['pagination']
        ];
    }

    /**
     * @param int $wateringId
     * @return Watering|null
     */
    public function find(int $wateringId): ?Watering
    {
        return $this->wateringRepository->find($wateringId);
    }

    /**
     * @param int $wateringId
     * @return WateringModel|null
     */
    public function findModel(int $wateringId): ?WateringModel
    {
        $watering = $this->wateringRepository->find($wateringId);

        return $this->toModel($watering);
    }

    /**
     * @return WateringModel[]
     */
    public function findAll(): array
    {
        $waterings = $this->wateringRepository->findAll();

        return array_map(
            fn (Watering $watering): WateringModel => $this->toModel($watering, true),
            $waterings
        );
    }

    /**
     * @return WateringModel[]
     */
    public function findAllByGroupId(?int $groupId = null): array
    {
        $waterings = $this->wateringRepository->findAllByGroupId($groupId);

        return array_map(
            fn (Watering $watering): WateringModel => $this->toModel($watering, true),
            $waterings
        );
    }

    /**
     * @param string $type
     * @return WateringModel[]
     */
    public function findWateringsByType(string $type): array
    {
        $waterings = $this->wateringRepository->findWateringsByType($type);

        return array_map(
            fn (Watering $watering): WateringModel => $this->toModel($watering),
            $waterings
        );
    }

    /**
     * @param string $method
     * @return WateringModel[]
     */
    public function findWateringsByMethod(string $method): array
    {
        $waterings = $this->wateringRepository->findWateringsByMethod($method);

        return array_map(
            fn (Watering $watering): WateringModel => $this->toModel($watering),
            $waterings
        );
    }

    /**
     * @param DateTimeImmutable $wateredAt
     * @return WateringModel[]
     */
    public function findWateringsByWateredAt(DateTimeImmutable $wateredAt): array
    {
        $waterings = $this->wateringRepository->findWateringsByWateredAt($wateredAt);

        return array_map(
            fn (Watering $watering): WateringModel => $this->toModel($watering),
            $waterings
        );
    }

    /**
     * @param Watering $watering
     * @return int
     */
    public function create(Watering $watering): int
    {
        return $this->wateringRepository->create($watering);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->wateringRepository->update();
    }

    /**
     * @param Watering $watering
     * @return void
     */
    public function remove(Watering $watering): void
    {
        $this->wateringRepository->remove($watering);
    }

    /**
     * @param Watering $watering
     * @param bool $addRelations
     * @return WateringModel
     */
    public function toModel(Watering $watering, bool $addRelations = false): WateringModel
    {
        $groupModel = null;
        $markerModel = null;

        if ($addRelations) {
            $groupModel = $this->groupRepository->toModel($watering->getGroup());
            $markerModel = $this->markerRepository->toModel($watering->getMarker());
        }

        return WateringModel::fromEntity($watering, $groupModel, $markerModel);
    }
}
