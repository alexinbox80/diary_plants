<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Fertilizer;
use App\Domain\Model\Fertilizer\FertilizerModel;
use App\Domain\Repository\FertilizerRepositoryInterface;
use DateTime;

class FertilizerRepositoryDecorator implements FertilizerRepositoryInterface
{
    public function __construct(
        private readonly FertilizerRepository $fertilizerRepository,
    ) {
    }

    /**
     * @return FertilizerModel[]
     */
    public function getFertilizersPaginated(int $page, int $perPage): array
    {
        $fertilizers = $this->fertilizerRepository->getFertilizersPaginated($page, $perPage);

        return array_map(
            static fn (Fertilizer $fertilizer): FertilizerModel => new FertilizerModel(
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
            ),
            $fertilizers
        );
    }

    /**
     * @param int $fertilizerId
     * @return Fertilizer|null
     */
    public function find(int $fertilizerId): ?Fertilizer
    {
        return $this->fertilizerRepository->find($fertilizerId);
    }

    /**
     * @param int $fertilizerId
     * @return FertilizerModel|null
     */
    public function findModel(int $fertilizerId): ?FertilizerModel
    {
        $fertilizer = $this->fertilizerRepository->find($fertilizerId);

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
     * @return FertilizerModel[]
     */
    public function findAll(): array
    {
        $fertilizer = $this->fertilizerRepository->findAll();

        return array_map(
            static fn (Fertilizer $fertilizer): FertilizerModel => new FertilizerModel(
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
            ),
            $fertilizer
        );
    }

    /**
     * @param string $title
     * @return FertilizerModel[]
     */
    public function findFertilizersByTitle(string $title): array
    {
        $fertilizers = $this->fertilizerRepository->findFertilizersByTitle($title);

        return array_map(
            static fn (Fertilizer $fertilizer): FertilizerModel => new FertilizerModel(
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
            ),
            $fertilizers
        );
    }

    /**
     * @param string $manufacturer
     * @return FertilizerModel[]
     */
    public function findFertilizersByManufacturer(string $manufacturer): array
    {
        $fertilizers = $this->fertilizerRepository->findFertilizersByTitle($manufacturer);

        return array_map(
            static fn (Fertilizer $fertilizer): FertilizerModel => new FertilizerModel(
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
            ),
            $fertilizers
        );
    }

    /**
     * @param DateTime $date
     * @return FertilizerModel[]
     */
    public function findFertilizersByUseDate(DateTime $date): array
    {
        $fertilizers = $this->fertilizerRepository->findFertilizersByUseDate($date);

        return array_map(
            static fn (Fertilizer $fertilizer): FertilizerModel => new FertilizerModel(
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
            ),
            $fertilizers
        );
    }

    /**
     * @param Fertilizer $fertilizer
     * @return int
     */
    public function create(Fertilizer $fertilizer): int
    {
        return $this->fertilizerRepository->create($fertilizer);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->fertilizerRepository->update();
    }

    /**
     * @param Fertilizer $fertilizer
     * @return void
     */
    public function remove(Fertilizer $fertilizer): void
    {
        $this->fertilizerRepository->remove($fertilizer);
    }
}
