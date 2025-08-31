<?php

namespace App\Domain\Service;

use App\Domain\Entity\Plant;
use App\Domain\Model\Plant\CreatePlantModel;
use App\Domain\Model\Plant\UpdatePlantModel;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Model\Price;
use App\Domain\Repository\PlantRepositoryInterface;
use Psr\Cache\InvalidArgumentException;

class PlantService
{
    public function __construct(
        private readonly PlantRepositoryInterface $plantRepository
    )
    {
    }

    /**
     * @param int $plantId
     * @return ?Plant
     */
    public function find(int $plantId): ?Plant
    {
        return $this->plantRepository->find($plantId);
    }

    /**
     * @return Plant[]
     */
    public function findAll(): array
    {
        return $this->plantRepository->findAll();
    }

    /**
     * @param string $title
     * @return PlantModel[]
     */
    public function findPlantsByTitle(string $title): array
    {
        return $this->plantRepository->findPlantsByTitle($title);
    }

    /**
     * @param Price $price
     * @return PlantModel[]
     */
    public function findPlantsByPrice(Price $price): array
    {
        return $this->plantRepository->findPlantsByPrice($price);
    }

    /**
     * @return PlantModel[]
     * @throws InvalidArgumentException
     */
    public function getPlantsPaginated(int $page, int $perPage): array
    {
        return $this->plantRepository->getPlantsPaginated($page, $perPage);
    }

    /**
     * @param CreatePlantModel $createPlantModel
     * @return PlantModel
     * @throws InvalidArgumentException
     */
    public function create(CreatePlantModel $createPlantModel): PlantModel
    {
        $plant = new Plant(
            $createPlantModel->title,
            $createPlantModel->room,
            $createPlantModel->isShown,
            $createPlantModel->description,
            $createPlantModel->purchaseDate,
            $createPlantModel->vaccinationDate,
            $createPlantModel->plantingDate,
            $createPlantModel->seller,
            $createPlantModel->nursery,
            $createPlantModel->price,
            $createPlantModel->shipping_cost,
            $createPlantModel->packaging_cost,
            $createPlantModel->soil,
            $createPlantModel->comment
        );

        $this->plantRepository->create($plant);

        return new PlantModel(
            $plant->getId(),
            $plant->getOid(),
            $plant->getTitle(),
            $plant->getRoom(),
            $plant->isShown(),
            $plant->getDescription(),
            $plant->getPurchaseDate(),
            $plant->getVaccinationDate(),
            $plant->getPlantingDate(),
            $plant->getSeller(),
            $plant->getNursery(),
            $plant->getPrice(),
            $plant->getShippingCost(),
            $plant->getPackagingCost(),
            $plant->getSoil(),
            $plant->getComment(),
            $plant->getCreatedAt(),
            $plant->getUpdatedAt()
        );
    }

    /**
     * @param Plant $plant
     * @param UpdatePlantModel $updatePlantModel
     * @return PlantModel
     * @throws InvalidArgumentException
     */
    public function update(Plant $plant, UpdatePlantModel $updatePlantModel): PlantModel
    {
        $plant->changeFields(
            $updatePlantModel->title,
            $updatePlantModel->room,
            $updatePlantModel->isShown,
            $updatePlantModel->description,
            $updatePlantModel->purchaseDate,
            $updatePlantModel->vaccinationDate,
            $updatePlantModel->plantingDate,
            $updatePlantModel->seller,
            $updatePlantModel->nursery,
            $updatePlantModel->price,
            $updatePlantModel->shipping_cost,
            $updatePlantModel->packaging_cost,
            $updatePlantModel->soil,
            $updatePlantModel->comment
        );

        $this->plantRepository->update();

        return new PlantModel(
            $plant->getId(),
            $plant->getOid(),
            $plant->getTitle(),
            $plant->getRoom(),
            $plant->isShown(),
            $plant->getDescription(),
            $plant->getPurchaseDate(),
            $plant->getVaccinationDate(),
            $plant->getPlantingDate(),
            $plant->getSeller(),
            $plant->getNursery(),
            $plant->getPrice(),
            $plant->getShippingCost(),
            $plant->getPackagingCost(),
            $plant->getSoil(),
            $plant->getComment(),
            $plant->getCreatedAt(),
            $plant->getUpdatedAt()
        );
    }

    /**
     * @param int $plantId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $plantId): void
    {
        $plant = $this->plantRepository->find($plantId);
        if ($plant !== null) {
            $this->plantRepository->remove($plant);
        }
    }

    /**
     * @param Plant $plant
     * @return void
     * @throws InvalidArgumentException
     */
    public function removePlant(Plant $plant): void
    {
        $this->plantRepository->remove($plant);
    }
}
