<?php

namespace App\Domain\Service;

use App\Domain\Entity\Fertilizer;
use App\Domain\Model\Fertilizer\CreateFertilizerModel;
use App\Domain\Model\Fertilizer\UpdateFertilizerModel;
use App\Domain\Model\Fertilizer\FertilizerModel;
use App\Domain\Repository\FertilizerRepositoryInterface;
use DateTime;
use Psr\Cache\InvalidArgumentException;

class FertilizerService
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly FertilizerRepositoryInterface $fertilizerRepository
    ) {
    }

    /**
     * @param int $fertilizerId
     * @return ?Fertilizer
     */
    public function find(int $fertilizerId): ?Fertilizer
    {
        return $this->fertilizerRepository->find($fertilizerId);
    }

    /**
     * @return Fertilizer[]
     */
    public function findAll(): array
    {
        return $this->fertilizerRepository->findAll();
    }

    /**
     * @param string $title
     * @return FertilizerModel[]
     */
    public function findFertilizersByTitle(string $title): array
    {
        return $this->fertilizerRepository->findFertilizersByTitle($title);
    }

    /**
     * @param string $manufacturer
     * @return FertilizerModel[]
     */
    public function findFertilizersByManufacturer(string $manufacturer): array
    {
        return $this->fertilizerRepository->findFertilizersByManufacturer($manufacturer);
    }

    /**
     * @param DateTime $date
     * @return FertilizerModel[]
     */
    public function findFertilizersByUseDate(DateTime $date): array
    {
        return $this->fertilizerRepository->findFertilizersByUseDate($date);
    }

    /**
     * @return FertilizerModel[]
     * @throws InvalidArgumentException
     */
    public function getPestsPaginated(int $page, int $perPage): array
    {
        return $this->fertilizerRepository->getFertilizersPaginated($page, $perPage);
    }

    /**
     * @param CreateFertilizerModel $createFertilizerModel
     * @return FertilizerModel
     * @throws InvalidArgumentException
     */
    public function create(CreateFertilizerModel $createFertilizerModel): FertilizerModel
    {
        $plant = $this->plantService->find($createFertilizerModel->plantId);

        $fertilizer= new Fertilizer(
            $plant,
            $createFertilizerModel->title,
            $createFertilizerModel->quantity,
            $createFertilizerModel->letter,
            $createFertilizerModel->manufacturer,
            $createFertilizerModel->description,
            $createFertilizerModel->comment
        );

        $this->fertilizerRepository->create($fertilizer);

        return new FertilizerModel(
            $fertilizer->getId(),
            $fertilizer->getPlant()->getId(),
            $fertilizer->getTitle(),
            $fertilizer->getQuantity(),
            $fertilizer->getLetter(),
            $fertilizer->getManufacturer(),
            $fertilizer->getDescription(),
            $fertilizer->getComment(),
            $fertilizer->getCreatedAt(),
            $fertilizer->getUpdatedAt()
        );
    }

    /**
     * @param Fertilizer $fertilizer
     * @param UpdateFertilizerModel $updateFertilizerModel
     * @return FertilizerModel
     * @throws InvalidArgumentException
     */
    public function update(Fertilizer $fertilizer, UpdateFertilizerModel $updateFertilizerModel): FertilizerModel
    {
        $plant = $this->plantService->find($updateFertilizerModel->plantId);

        $fertilizer->changeFieldsWithPlant(
            $updateFertilizerModel->title,
            $updateFertilizerModel->quantity,
            $updateFertilizerModel->letter,
            $updateFertilizerModel->manufacturer,
            $plant,
            $updateFertilizerModel->description,
            $updateFertilizerModel->comment
        );

        $this->fertilizerRepository->update();

        return  new FertilizerModel(
            $fertilizer->getId(),
            $fertilizer->getPlant()->getId(),
            $fertilizer->getTitle(),
            $fertilizer->getQuantity(),
            $fertilizer->getLetter(),
            $fertilizer->getManufacturer(),
            $fertilizer->getDescription(),
            $fertilizer->getComment(),
            $fertilizer->getCreatedAt(),
            $fertilizer->getUpdatedAt()
    );
    }

    /**
     * @param int $fertilizerId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $fertilizerId): void
    {
        $fertilizer = $this->fertilizerRepository->find($fertilizerId);
        if ($fertilizer !== null) {
            $this->fertilizerRepository->remove($fertilizer);
        }
    }

    /**
     * @param Fertilizer $fertilizer
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeFertilizer(Fertilizer $fertilizer): void
    {
        $this->fertilizerRepository->remove($fertilizer);
    }
}
