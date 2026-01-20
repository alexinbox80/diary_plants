<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Stimulant;
use App\Domain\Model\Stimulant\StimulantModel;
use App\Domain\Repository\StimulantRepositoryInterface;
use DateTimeImmutable;

class StimulantRepositoryDecorator implements StimulantRepositoryInterface
{
    public function __construct(
        private readonly StimulantRepository $stimulantRepository,
    ) {
    }

    /**
     * @return StimulantModel[]
     */
    public function getStimulantsPaginated(int $page, int $perPage): array
    {
        $stimulants = $this->stimulantRepository->getStimulantsPaginated($page, $perPage);

        return array_map(
            static fn (Stimulant $stimulant): StimulantModel => new StimulantModel(
                $stimulant->getId(),
                $stimulant->getPlant()->getId(),
                $stimulant->getTitle(),
                $stimulant->getQuantity(),
                $stimulant->getLetter(),
                $stimulant->getManufacturer(),
                $stimulant->getDescription(),
                $stimulant->getComment(),
                $stimulant->getCreatedAt(),
                $stimulant->getUpdatedAt()
            ),
            $stimulants
        );
    }

    /**
     * @param int $stimulantId
     * @return Stimulant|null
     */
    public function find(int $stimulantId): ?Stimulant
    {
        return $this->stimulantRepository->find($stimulantId);
    }

    /**
     * @param int $stimulantId
     * @return StimulantModel|null
     */
    public function findModel(int $stimulantId): ?StimulantModel
    {
        $stimulant = $this->stimulantRepository->find($stimulantId);

        return new StimulantModel(
            $stimulant->getId(),
            $stimulant->getPlant()->getId(),
            $stimulant->getTitle(),
            $stimulant->getQuantity(),
            $stimulant->getLetter(),
            $stimulant->getManufacturer(),
            $stimulant->getDescription(),
            $stimulant->getComment(),
            $stimulant->getCreatedAt(),
            $stimulant->getUpdatedAt()
        );
    }

    /**
     * @return StimulantModel[]
     */
    public function findAll(): array
    {
        $stimulant = $this->stimulantRepository->findAll();

        return array_map(
            static fn (Stimulant $stimulant): StimulantModel => new StimulantModel(
                $stimulant->getId(),
                $stimulant->getPlant()->getId(),
                $stimulant->getTitle(),
                $stimulant->getQuantity(),
                $stimulant->getLetter(),
                $stimulant->getManufacturer(),
                $stimulant->getDescription(),
                $stimulant->getComment(),
                $stimulant->getCreatedAt(),
                $stimulant->getUpdatedAt()
            ),
            $stimulant
        );
    }

    /**
     * @param string $title
     * @return StimulantModel[]
     */
    public function findStimulantsByTitle(string $title): array
    {
        $stimulants = $this->stimulantRepository->findStimulantsByTitle($title);

        return array_map(
            static fn (Stimulant $stimulant): StimulantModel => new StimulantModel(
                $stimulant->getId(),
                $stimulant->getPlant()->getId(),
                $stimulant->getTitle(),
                $stimulant->getQuantity(),
                $stimulant->getLetter(),
                $stimulant->getManufacturer(),
                $stimulant->getDescription(),
                $stimulant->getComment(),
                $stimulant->getCreatedAt(),
                $stimulant->getUpdatedAt()
            ),
            $stimulants
        );
    }

    /**
     * @param string $manufacturer
     * @return StimulantModel[]
     */
    public function findStimulantsByManufacturer(string $manufacturer): array
    {
        $stimulants = $this->stimulantRepository->findStimulantsByTitle($manufacturer);

        return array_map(
            static fn (Stimulant $stimulant): StimulantModel => new StimulantModel(
                $stimulant->getId(),
                $stimulant->getPlant()->getId(),
                $stimulant->getTitle(),
                $stimulant->getQuantity(),
                $stimulant->getLetter(),
                $stimulant->getManufacturer(),
                $stimulant->getDescription(),
                $stimulant->getComment(),
                $stimulant->getCreatedAt(),
                $stimulant->getUpdatedAt()
            ),
            $stimulants
        );
    }

    /**
     * @param DateTimeImmutable $date
     * @return StimulantModel[]
     */
    public function findStimulantsByUseDate(DateTimeImmutable $date): array
    {
        $stimulants = $this->stimulantRepository->findStimulantsByUseDate($date);

        return array_map(
            static fn (Stimulant $stimulant): StimulantModel => new StimulantModel(
                $stimulant->getId(),
                $stimulant->getPlant()->getId(),
                $stimulant->getTitle(),
                $stimulant->getQuantity(),
                $stimulant->getLetter(),
                $stimulant->getManufacturer(),
                $stimulant->getDescription(),
                $stimulant->getComment(),
                $stimulant->getCreatedAt(),
                $stimulant->getUpdatedAt()
            ),
            $stimulants
        );
    }

    /**
     * @param Stimulant $stimulant
     * @return int
     */
    public function create(Stimulant $stimulant): int
    {
        return $this->stimulantRepository->create($stimulant);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->stimulantRepository->update();
    }

    /**
     * @param Stimulant $stimulant
     * @return void
     */
    public function remove(stimulant $stimulant): void
    {
        $this->stimulantRepository->remove($stimulant);
    }
}
