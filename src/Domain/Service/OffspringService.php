<?php

namespace App\Domain\Service;

use App\Domain\Entity\Offspring;
use App\Domain\Model\Offspring\CreateOffspringModel;
use App\Domain\Model\Offspring\UpdateOffspringModel;
use App\Domain\Model\Offspring\OffspringModel;
use App\Domain\Repository\OffspringRepositoryInterface;
use Psr\Cache\InvalidArgumentException;

class OffspringService
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly OffspringRepositoryInterface $offspringRepository
    ) {
    }

    /**
     * @param int $offspringId
     * @return ?Offspring
     */
    public function find(int $offspringId): ?Offspring
    {
        return $this->offspringRepository->find($offspringId);
    }

    /**
     * @return Offspring[]
     */
    public function findAll(): array
    {
        return $this->offspringRepository->findAll();
    }

    /**
     * @param string $mass
     * @return OffspringModel[]
     */
    public function findStatusesByMass(string $mass): array
    {
        return $this->offspringRepository->findOffspringsByMass($mass);
    }

    /**
     * @param string $flavor
     * @return OffspringModel[]
     */
    public function findStatusesByFlavor(string $flavor): array
    {
        return $this->offspringRepository->findOffspringsByFlavor($flavor);
    }

    /**
     * @param string $color
     * @return OffspringModel[]
     */
    public function findStatusesByColor(string $color): array
    {
        return $this->offspringRepository->findOffspringsByColor($color);
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public function getOffspringsPaginated(int $page, int $perPage): array
    {
        return $this->offspringRepository->getOffspringsPaginated($page, $perPage);
    }

    /**
     * @param CreateOffspringModel $createOffspringModel
     * @return OffspringModel
     * @throws InvalidArgumentException
     */
    public function create(CreateOffspringModel $createOffspringModel): OffspringModel
    {
        $plant = $this->plantService->find($createOffspringModel->plantId);

        $offspring = new Offspring(
            $plant,
            $createOffspringModel->fruitingDate,
            $createOffspringModel->floweringDate,
            $createOffspringModel->mass,
            $createOffspringModel->color,
            $createOffspringModel->flavor,
            $createOffspringModel->quantity,
            $createOffspringModel->comment,
        );

        $this->offspringRepository->create($offspring);

        return new OffspringModel(
            $offspring->getId(),
            $offspring->getPlant()->getId(),
            $offspring->getFruitingDate(),
            $offspring->getFloweringDate(),
            $offspring->getMass(),
            $offspring->getColor(),
            $offspring->getFlavor(),
            $offspring->getQuantity(),
            $offspring->getComment(),
            $offspring->getCreatedAt(),
            $offspring->getUpdatedAt()
        );
    }

    /**
     * @param Offspring $offspring
     * @param UpdateOffspringModel $updateOffspringModel
     * @return OffspringModel
     * @throws InvalidArgumentException
     */
    public function update(Offspring $offspring, UpdateOffspringModel $updateOffspringModel): OffspringModel
    {
        $plant = $this->plantService->find($updateOffspringModel->plantId);

        $offspring->changeFields(
            $plant,
            $updateOffspringModel->fruitingDate,
            $updateOffspringModel->floweringDate,
            $updateOffspringModel->mass,
            $updateOffspringModel->color,
            $updateOffspringModel->flavor,
            $updateOffspringModel->quantity,
            $updateOffspringModel->comment,
        );

        $this->offspringRepository->update();

        return new OffspringModel(
            $offspring->getId(),
            $offspring->getPlant()->getId(),
            $offspring->getFruitingDate(),
            $offspring->getFloweringDate(),
            $offspring->getMass(),
            $offspring->getColor(),
            $offspring->getFlavor(),
            $offspring->getQuantity(),
            $offspring->getComment(),
            $offspring->getCreatedAt(),
            $offspring->getUpdatedAt()
        );
    }

    /**
     * @param int $offspringId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $offspringId): void
    {
        $offspring = $this->offspringRepository->find($offspringId);
        if ($offspring !== null) {
            $this->offspringRepository->remove($offspring);
        }
    }

    /**
     * @param Offspring $offspring
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeOffspring(Offspring $offspring): void
    {
        $this->offspringRepository->remove($offspring);
    }
}
