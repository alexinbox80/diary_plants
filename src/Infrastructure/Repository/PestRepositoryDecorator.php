<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Pest;
use App\Domain\Model\Pest\PestModel;
use App\Domain\Repository\PestRepositoryInterface;
use DateTime;

class PestRepositoryDecorator implements PestRepositoryInterface
{
    public function __construct(
        private readonly PestRepository $pestRepository,
    ) {
    }

    /**
     * @return PestModel[]
     */
    public function getPestsPaginated(int $page, int $perPage): array
    {
        $pests = $this->pestRepository->getPestsPaginated($page, $perPage);

        return array_map(
            static fn (Pest $pest): PestModel => new PestModel(
                $pest->getId(),
                $pest->getPlant()->getId(),
                $pest->getTitle(),
                $pest->getQuantity(),
                $pest->getUseDate(),
                $pest->getManufacturer(),
                $pest->getDescription(),
                $pest->getComment(),
                $pest->getCreatedAt(),
                $pest->getUpdatedAt()
            ),
            $pests
        );
    }

    /**
     * @param int $pestId
     * @return Pest|null
     */
    public function find(int $pestId): ?Pest
    {
        return $this->pestRepository->find($pestId);
    }

    /**
     * @param int $pestId
     * @return PestModel|null
     */
    public function findModel(int $pestId): ?PestModel
    {
        $pest = $this->pestRepository->find($pestId);

        return new PestModel(
            $pest->getId(),
            $pest->getPlant()->getId(),
            $pest->getTitle(),
            $pest->getManufacturer(),
            $pest->getQuantity(),
            $pest->getUseDate(),
            $pest->getDescription(),
            $pest->getComment(),
            $pest->getCreatedAt(),
            $pest->getUpdatedAt()
        );
    }

    /**
     * @return PestModel[]
     */
    public function findAll(): array
    {
        $pest = $this->pestRepository->findAll();

        return array_map(
            static fn (Pest $pest): PestModel => new PestModel(
                $pest->getId(),
                $pest->getPlant()->getId(),
                $pest->getTitle(),
                $pest->getManufacturer(),
                $pest->getQuantity(),
                $pest->getUseDate(),
                $pest->getDescription(),
                $pest->getComment(),
                $pest->getCreatedAt(),
                $pest->getUpdatedAt()
            ),
            $pest
        );
    }

    /**
     * @param string $title
     * @return PestModel[]
     */
    public function findPestsByTitle(string $title): array
    {
        $pests = $this->pestRepository->findPestsByTitle($title);

        return array_map(
            static fn (Pest $pest): PestModel => new PestModel(
                $pest->getId(),
                $pest->getPlant()->getId(),
                $pest->getTitle(),
                $pest->getQuantity(),
                $pest->getUseDate(),
                $pest->getManufacturer(),
                $pest->getDescription(),
                $pest->getComment(),
                $pest->getCreatedAt(),
                $pest->getUpdatedAt()
            ),
            $pests
        );
    }

    /**
     * @param string $manufacturer
     * @return PestModel[]
     */
    public function findPestsByManufacturer(string $manufacturer): array
    {
        $pests = $this->pestRepository->findPestsByTitle($manufacturer);

        return array_map(
            static fn (Pest $pest): PestModel => new PestModel(
                $pest->getId(),
                $pest->getPlant()->getId(),
                $pest->getTitle(),
                $pest->getQuantity(),
                $pest->getUseDate(),
                $pest->getManufacturer(),
                $pest->getDescription(),
                $pest->getComment(),
                $pest->getCreatedAt(),
                $pest->getUpdatedAt()
            ),
            $pests
        );
    }

    /**
     * @param DateTime $date
     * @return PestModel[]
     */
    public function findPestsByUseDate(DateTime $date): array
    {
        $pests = $this->pestRepository->findPestsByUseDate($date);

        return array_map(
            static fn (Pest $pest): PestModel => new PestModel(
                $pest->getId(),
                $pest->getPlant()->getId(),
                $pest->getTitle(),
                $pest->getQuantity(),
                $pest->getUseDate(),
                $pest->getManufacturer(),
                $pest->getDescription(),
                $pest->getComment(),
                $pest->getCreatedAt(),
                $pest->getUpdatedAt()
            ),
            $pests
        );
    }

    /**
     * @param Pest $pest
     * @return int
     */
    public function create(Pest $pest): int
    {
        return $this->pestRepository->create($pest);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->pestRepository->update();
    }

    /**
     * @param Pest $pest
     * @return void
     */
    public function remove(Pest $pest): void
    {
        $this->pestRepository->remove($pest);
    }
}
