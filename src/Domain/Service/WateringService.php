<?php

namespace App\Domain\Service;

use DateTimeImmutable;
use App\Domain\Entity\Watering;
use Psr\Cache\InvalidArgumentException;
use App\Domain\Model\Watering\WateringModel;
use App\Domain\Model\Watering\CreateWateringModel;
use App\Domain\Model\Watering\UpdateWateringModel;
use App\Domain\ValueObject\Enum\Watering\WaterType;
use App\Domain\ValueObject\Watering\WateringDetails;
use App\Domain\Repository\WateringRepositoryInterface;
use App\Domain\ValueObject\Enum\Watering\WateringMethod;
use App\Controller\Web\Dashboard\Watering\EditWatering\Input\EditWateringDTO;
use App\Controller\Web\Dashboard\Watering\CreateWatering\Input\CreateWateringDTO;

class WateringService
{
    public function __construct(
        private readonly GroupService $groupService,
        private readonly MarkerService $markerService,
        private readonly ModelFactory $modelFactory,
        private readonly WateringRepositoryInterface $wateringRepository
    ) {
    }

    /**
     * @param int $wateringId
     * @return ?Watering
     */
    public function find(int $wateringId): ?Watering
    {
        return $this->wateringRepository->find($wateringId);
    }

    /**
     * @return Watering[]
     */
    public function findAll(): array
    {
        return $this->wateringRepository->findAll();
    }

    /**
     * @param string $type
     * @return WateringModel[]
     */
    public function findWateringsByType(string $type): array
    {
        return $this->wateringRepository->findWateringsByType($type);
    }

    /**
     * @param string $method
     * @return WateringModel[]
     */
    public function findWateringsByMethod(string $method): array
    {
        return $this->wateringRepository->findWateringsByMethod($method);
    }

    /**
     * @param DateTimeImmutable $wateredAt
     * @return WateringModel[]
     */
    public function findWateringsByWateredAt(DateTimeImmutable $wateredAt): array
    {
        return $this->wateringRepository->findWateringsByWateredAt($wateredAt);
    }

    /**
     * @return WateringModel[]
     * @throws InvalidArgumentException
     */
    public function getWateringsPaginated(int $page, int $perPage): array
    {
        return $this->wateringRepository->getWateringsPaginated($page, $perPage);
    }

    /**
     * @param CreateWateringModel $createWateringModel
     * @return WateringModel
     * @throws InvalidArgumentException
     */
    public function create(CreateWateringModel $createWateringModel): WateringModel
    {
        $group = $this->groupService->find($createWateringModel->groupId);
        $marker = $this->markerService->find($createWateringModel->markerId);

        $watering = new Watering(
            $group,
            $marker,
            new WateringDetails(
                $createWateringModel->amount,
                WaterType::from($createWateringModel->waterType),
                WateringMethod::from($createWateringModel->wateringMethod),
                $createWateringModel->temperature,
            )
        );

        $watering
            ->setDescription($createWateringModel->description)
            ->setComment($createWateringModel->comment);

        $this->wateringRepository->create($watering);

        return $this->wateringRepository->toModel($watering);
    }

    /**
     * @param CreateWateringDTO $dto
     * @return WateringModel
     */
    public function createFromCreateWateringDTO(CreateWateringDTO $dto): WateringModel
    {
        $model = $this->modelFactory->makeModel(
            CreateWateringModel::class,
            2,
            $dto->markerId,
            $dto->amount,
            $dto->waterType,
            $dto->wateringMethod,
            $dto->wateredAt,
            $dto->temperature,
            $dto->description,
            $dto->comment
        );

        return $this->create($model);
    }

    /**
     * @param Watering $watering
     * @param UpdateWateringModel $updateWateringModel
     * @return WateringModel
     * @throws InvalidArgumentException
     */
    public function update(Watering $watering, UpdateWateringModel $updateWateringModel): WateringModel
    {
        $group = $this->groupService->find($updateWateringModel->groupId);
        $marker = $this->markerService->find($updateWateringModel->markerId);

        $watering->updateFields(
            $group,
            $marker,
            new WateringDetails(
                $updateWateringModel->amount,
                WaterType::from($updateWateringModel->waterType),
                WateringMethod::from($updateWateringModel->wateringMethod),
                $updateWateringModel->temperature,
            ))
            ->setDescription($updateWateringModel->description)
            ->setComment($updateWateringModel->comment);

        $this->wateringRepository->update();

        return $this->wateringRepository->toModel($watering);
    }

    /**
     * @param Watering $watering
     * @param EditWateringDTO $dto
     * @return WateringModel
     */
    public function updateFromEditWateringDTO(Watering $watering, EditWateringDTO $dto): WateringModel
    {
        $model = $this->modelFactory->makeModel(
            UpdateWateringModel::class,
            2,
            $dto->markerId,
            $dto->amount,
            $dto->waterType,
            $dto->wateringMethod,
            $dto->wateredAt,
            $dto->temperature,
            $dto->description,
            $dto->comment
        );

        return $this->update($watering, $model);
    }

    /**
     * @param int $wateringId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $wateringId): void
    {
        $watering = $this->wateringRepository->find($wateringId);
        if ($watering !== null) {
            $this->wateringRepository->remove($watering);
        }
    }

    /**
     * @param Watering $watering
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeWatering(Watering $watering): void
    {
        $this->wateringRepository->remove($watering);
    }
}
