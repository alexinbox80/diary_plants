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
use App\Controller\Web\Dashboard\Stimulant\EditStimulant\Input\EditStimulantDTO;
use App\Controller\Web\Dashboard\Stimulant\CreateStimulant\Input\CreateStimulantDTO;

class StimulantService
{
    public function __construct(
        private readonly GroupService $groupService,
        private readonly MarkerService $markerService,
        private readonly ModelFactory $modelFactory,
        private readonly StimulantRepositoryInterface $stimulantRepository
    ) {
    }

    /**
     * @param int $groupId
     * @return StimulantModel[]
     */
    public function getStimulantsForDairy($groupId): array
    {
        return $this->stimulantRepository->getStimulantsForDairy($groupId);
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
        $marker = $this->markerService->find($createStimulantModel->markerId);

        $stimulant = new Stimulant(
            $group,
            $marker,
            $createStimulantModel->title,
            new PreparationVolume(
                $createStimulantModel->amount,
                $createStimulantModel->applicationRate
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
     * @param CreateStimulantDTO $dto
     * @return StimulantModel
     */
    public function createFromCreateStimulantDTO(CreateStimulantDTO $dto): StimulantModel
    {
        $model = $this->modelFactory->makeModel(
            CreateStimulantModel::class,
            2,
            $dto->markerId,
            $dto->title,
            $dto->amount,
            $dto->applicationRate,
            $dto->manufacturer,
            $dto->description,
            $dto->comment
        );

        return $this->create($model);
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
        $marker = $this->markerService->find($updateStimulantModel->markerId);

        $stimulant->changeFieldsWithMarker(
            $group,
            $marker,
            $updateStimulantModel->title,
            new PreparationVolume(
                $updateStimulantModel->amount,
                $updateStimulantModel->applicationRate
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
     * @param Stimulant $stimulant
     * @param EditStimulantDTO $dto
     * @return StimulantModel
     */
    public function updateFromEditStimulantDTO(Stimulant $stimulant, EditStimulantDTO $dto): StimulantModel
    {
        $model = $this->modelFactory->makeModel(
            UpdateStimulantModel::class,
            2,
            $dto->markerId,
            $dto->title,
            $dto->amount,
            $dto->applicationRate,
            $dto->manufacturer,
            $dto->description,
            $dto->comment
        );

        return $this->update($stimulant, $model);
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
