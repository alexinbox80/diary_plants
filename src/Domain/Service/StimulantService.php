<?php

namespace App\Domain\Service;

use DateTimeImmutable;
use App\Domain\Entity\Stimulant;
use Psr\Cache\InvalidArgumentException;
use App\Domain\Model\Stimulant\StimulantModel;
use App\Domain\Model\Stimulant\CreateStimulantModel;
use App\Domain\Model\Stimulant\UpdateStimulantModel;
use App\Domain\Repository\StimulantRepositoryInterface;
use App\Domain\ValueObject\Preparation\PreparationDetails;
use App\Domain\ValueObject\Preparation\PreparationVolume;

class StimulantService
{
    public function __construct(
        private readonly GroupService $groupService,
        private readonly PlantService $plantService,
        private readonly ModelFactory $modelFactory,
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
     * @param DateTimeImmutable $date
     * @return StimulantModel[]
     */
    public function findStimulantsByUseDate(DateTimeImmutable $date): array
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
        $group = $this->groupService->find($createStimulantModel->groupId);
        $plant = $this->plantService->find($createStimulantModel->plantId);

        $stimulant = new Stimulant(
            $group,
            $plant,
            $createStimulantModel->title,
            new PreparationVolume(
                $createStimulantModel->quantity,
                $createStimulantModel->letter
            ),
            new PreparationDetails(
                $createStimulantModel->manufacturer,
                $createStimulantModel->description,
                $createStimulantModel->comment
            )
        );

        $this->stimulantRepository->create($stimulant);

        return $this->stimulantRepository->toModel($stimulant);
    }

    /**
     * @param Stimulant $stimulant
     * @param UpdateStimulantModel $updateStimulantModel
     * @return StimulantModel
     * @throws InvalidArgumentException
     */
    public function update(Stimulant $stimulant, UpdateStimulantModel $updateStimulantModel): StimulantModel
    {
        $group = $this->groupService->find($updateStimulantModel->groupId);
        $plant = $this->plantService->find($updateStimulantModel->plantId);

        $stimulant->changeFieldsWithPlant(
            $group,
            $plant,
            $updateStimulantModel->title,
            new PreparationVolume(
                $updateStimulantModel->quantity,
                $updateStimulantModel->letter
            ),
            new PreparationDetails(
                $updateStimulantModel->manufacturer,
                $updateStimulantModel->description,
                $updateStimulantModel->comment
            )
        );

        $this->stimulantRepository->update();

        return $this->stimulantRepository->toModel($stimulant);
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
