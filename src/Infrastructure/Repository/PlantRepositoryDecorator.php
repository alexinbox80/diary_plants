<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Plant;
use App\Domain\Model\Price;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Repository\PlantRepositoryInterface;

class PlantRepositoryDecorator implements PlantRepositoryInterface
{
    public function __construct(
        private readonly PlantRepository $plantRepository,
    ) {
    }

    /**
     * @return PlantModel[]
     */
    public function getPlantsPaginated(int $page, int $perPage): array
    {
        $plants = $this->plantRepository->getPlantsPaginated($page, $perPage);

        return array_map(
            static fn (Plant $plant): PlantModel => new PlantModel(
                $plant->getId(),
                $plant->getOid(),
                $plant->getTitle(),
                $plant->getRoom(),
                $plant->isShown(),
                $plant->getDescription(),
                $plant->getQrCodeBase64(),
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
            ),
            $plants
        );
    }

    /**
     * @param int $plantId
     * @return Plant|null
     */
    public function find(int $plantId): ?Plant
    {
        return $this->plantRepository->find($plantId);
    }

    /**
     * @param int $plantId
     * @return PlantModel|null
     */
    public function findModel(int $plantId): ?PlantModel
    {
        $plant = $this->plantRepository->find($plantId);

        return new PlantModel(
            $plant->getId(),
            $plant->getOid(),
            $plant->getTitle(),
            $plant->getRoom(),
            $plant->isShown(),
            $plant->getDescription(),
            $plant->getQrCodeBase64(),
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
     * @return PlantModel[]
     */
    public function findAll(): array
    {
        $plants = $this->plantRepository->findAll();

        return array_map(
            static fn (Plant $plant): PlantModel => new PlantModel(
                $plant->getId(),
                $plant->getOid(),
                $plant->getTitle(),
                $plant->getRoom(),
                $plant->isShown(),
                $plant->getDescription(),
                $plant->getQrCodeBase64(),
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
            ),
            $plants
        );
    }

    /**
     * @param string $title
     * @return PlantModel[]
     */
    public function findPlantsByTitle(string $title): array
    {
        $plants = $this->plantRepository->findPlantsByTitle($title);

        return array_map(
            static fn (Plant $plant): PlantModel => new PlantModel(
                $plant->getId(),
                $plant->getOid(),
                $plant->getTitle(),
                $plant->getRoom(),
                $plant->isShown(),
                $plant->getDescription(),
                $plant->getQrCodeBase64(),
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
            ),
            $plants
        );
    }

    /**
     * @param Price $price
     * @return PlantModel[]
     */
    public function findPlantsByPrice(Price $price): array
    {
        $plants = $this->plantRepository->findPlantsByPrice($price);

        return array_map(
            static fn (Plant $plant): PlantModel => new PlantModel(
                $plant->getId(),
                $plant->getOid(),
                $plant->getTitle(),
                $plant->getRoom(),
                $plant->isShown(),
                $plant->getDescription(),
                $plant->getQrCodeBase64(),
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
            ),
            $plants
        );
    }

    /**
     * @param Plant $plant
     * @return int
     */
    public function create(Plant $plant): int
    {
        return $this->plantRepository->create($plant);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->plantRepository->update();
    }

    /**
     * @param Plant $plant
     * @return void
     */
    public function remove(Plant $plant): void
    {
        $this->plantRepository->remove($plant);
    }
}
