<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Offspring;
use App\Domain\Model\Offspring\OffspringModel;
use App\Domain\Repository\OffspringRepositoryInterface;

class OffspringRepositoryDecorator implements OffspringRepositoryInterface
{
    public function __construct(
        private readonly OffspringRepository $offspringRepository,
    ) {
    }

    /**
     * @return OffspringModel[]
     */
    public function getOffspringsPaginated(int $page, int $perPage): array
    {
        $offspringsPaginated = $this->offspringRepository->getOffspringsPaginated($page, $perPage);

        if (!is_array($offspringsPaginated['items'])) {
            throw new \InvalidArgumentException('Expected array for plants');
        }

        $offspringsModel = array_map(
            static fn (Offspring $offspring): OffspringModel => new OffspringModel(
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
            ),
            $offspringsPaginated['items']
        );

        return [
            'offspringsModel' => $offspringsModel,
            'pagination' => $offspringsPaginated['pagination']
        ];
    }

    /**
     * @param int $offspringId
     * @return OffspringModel|null
     */
    public function find(int $offspringId): ?OffspringModel
    {
        $offspring = $this->offspringRepository->find($offspringId);

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
     * @return OffspringModel[]
     */
    public function findAll(): array
    {
        $offsprings = $this->offspringRepository->findAll();

        return array_map(
            static fn (Offspring $offspring): OffspringModel => new OffspringModel(
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
            ),
            $offsprings
        );
    }

    /**
     * @param string $mass
     * @return OffspringModel[]
     */
    public function findOffspringsByMass(string $mass): array
    {
        $offsprings = $this->offspringRepository->findOffspringsByMass($mass);

        return array_map(
            static fn (Offspring $offspring): OffspringModel => new OffspringModel(
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
            ),
            $offsprings
        );
    }

    /**
     * @param string $flavor
     * @return OffspringModel[]
     */
    public function findOffspringsByFlavor(string $flavor): array
    {
        $offsprings = $this->offspringRepository->findOffspringsByFlavor($flavor);

        return array_map(
            static fn (Offspring $offspring): OffspringModel => new OffspringModel(
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
            ),
            $offsprings
        );
    }

    /**
     * @param string $color
     * @return OffspringModel[]
     */
    public function findOffspringsByColor(string $color): array
    {
        $offsprings = $this->offspringRepository->findOffspringsByColor($color);

        return array_map(
            static fn (Offspring $offspring): OffspringModel => new OffspringModel(
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
            ),
            $offsprings
        );
    }

    /**
     * @param Offspring $offspring
     * @return int
     */
    public function create(Offspring $offspring): int
    {
        return $this->offspringRepository->create($offspring);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->offspringRepository->update();
    }

    /**
     * @param Offspring $offspring
     * @return void
     */
    public function remove(Offspring $offspring): void
    {
        $this->offspringRepository->remove($offspring);
    }
}
