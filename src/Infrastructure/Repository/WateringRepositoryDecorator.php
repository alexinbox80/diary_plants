<?php

namespace App\Infrastructure\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Watering;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Marker\MarkerModel;
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
     * @return WateringModel[]
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
            $groupModel = $this->groupRepository->findModel($watering->getGroup()->getId());
            $markerModel = $this->markerRepository->findModel($watering->getMarker()->getId());
        }

        return self::makeWateringModel($watering, $groupModel, $markerModel);
    }

    /**
     * @param Watering $watering
     * @param GroupModel|null $groupModel
     * @param MarkerModel|null $markerModel
     * @return WateringModel
     */
    static function makeWateringModel(Watering $watering, ?GroupModel $groupModel = null, ?MarkerModel $markerModel = null): WateringModel
    {
        return new WateringModel(
            $watering->getId(),
            $watering->getGroup()->getId(),
            $watering->getMarker()->getId(),
            $watering->getDetails()->getAmount(),
            $watering->getDetails()->getType()->value,
            $watering->getDetails()->getMethod()->value,
            $watering->getWateredAt(),
            $watering->getDetails()->getTemperature(),
            $watering->getDescription(),
            $watering->getComment(),
            $groupModel,
            $markerModel,
            $watering->getCreatedAt(),
            $watering->getUpdatedAt()
        );
    }
}
