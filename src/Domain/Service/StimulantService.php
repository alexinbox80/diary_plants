<?php

namespace App\Domain\Service;

use App\Domain\Entity\Stimulant;
use App\Domain\Model\Stimulant\CreateStimulantModel;
use App\Domain\Model\Stimulant\UpdateStimulantModel;
use App\Domain\Model\Stimulant\StimulantModel;
use App\Domain\Repository\StimulantRepositoryInterface;
use DateTime;
use Psr\Cache\InvalidArgumentException;

class StimulantService
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly StimulantRepositoryInterface $stimulantRepository
    ) {
    }

    /**
     * @param int $stimulantId
     * @return ?Stimulant
     */
    public function find(int $stimulantId): ?Stimulant
    {
        return $this->stimulantRepository->find($stimulantId);
    }

    /**
     * @return Stimulant[]
     */
    public function findAll(): array
    {
        return $this->stimulantRepository->findAll();
    }

    /**
     * @param string $title
     * @return StimulantModel[]
     */
    public function findStimulantsByTitle(string $title): array
    {
        return $this->stimulantRepository->findStimulantsByTitle($title);
    }

    /**
     * @param string $manufacturer
     * @return StimulantModel[]
     */
    public function findStimulantsByManufacturer(string $manufacturer): array
    {
        return $this->stimulantRepository->findStimulantsByManufacturer($manufacturer);
    }

    /**
     * @param DateTime $date
     * @return StimulantModel[]
     */
    public function findStimulantsByUseDate(DateTime $date): array
    {
        return $this->stimulantRepository->findStimulantsByUseDate($date);
    }

    /**
     * @return StimulantModel[]
     * @throws InvalidArgumentException
     */
    public function getStimulantsPaginated(int $page, int $perPage): array
    {
        return $this->stimulantRepository->getStimulantsPaginated($page, $perPage);
    }

    /**
     * @param CreateStimulantModel $createStimulantModel
     * @return StimulantModel
     * @throws InvalidArgumentException
     */
    public function create(CreateStimulantModel $createStimulantModel): StimulantModel
    {
        $plant = $this->plantService->find($createStimulantModel->plantId);

        $stimulant = new Stimulant(
            $plant,
            $createStimulantModel->title,
            $createStimulantModel->quantity,
            $createStimulantModel->letter,
            $createStimulantModel->manufacturer,
            $createStimulantModel->description,
            $createStimulantModel->comment
        );

        $this->stimulantRepository->create($stimulant);

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
     * @param Stimulant $stimulant
     * @param UpdateStimulantModel $updateStimulantModel
     * @return StimulantModel
     * @throws InvalidArgumentException
     */
    public function update(Stimulant $stimulant, UpdateStimulantModel $updateStimulantModel): StimulantModel
    {
        $plant = $this->plantService->find($updateStimulantModel->plantId);

        $stimulant->changeFieldsWithPlant(
            $updateStimulantModel->title,
            $updateStimulantModel->quantity,
            $updateStimulantModel->letter,
            $plant,
            $updateStimulantModel->manufacturer,
            $updateStimulantModel->description,
            $updateStimulantModel->comment
        );

        $this->stimulantRepository->update();

        return  new StimulantModel(
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
     * @param int $stimulantId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $stimulantId): void
    {
        $stimulant = $this->stimulantRepository->find($stimulantId);
        if ($stimulant !== null) {
            $this->stimulantRepository->remove($stimulant);
        }
    }

    /**
     * @param Stimulant $stimulant
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeStimulant(Stimulant $stimulant): void
    {
        $this->stimulantRepository->remove($stimulant);
    }
}
