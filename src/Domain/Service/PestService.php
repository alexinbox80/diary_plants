<?php

namespace App\Domain\Service;

use DateTimeImmutable;
use App\Domain\Entity\Pest;
use App\Domain\Model\Pest\PestModel;
use App\Domain\Model\Pest\CreatePestModel;
use App\Domain\Model\Pest\UpdatePestModel;
use App\Domain\Repository\PestRepositoryInterface;
use App\Domain\ValueObject\Preparation\PreparationVolume;
use App\Domain\ValueObject\Preparation\PreparationDetails;
use App\Controller\Web\Dashboard\Pest\EditPest\Input\EditPestDTO;
use App\Controller\Web\Dashboard\Pest\CreatePest\Input\CreatePestDTO;

class PestService
{
    public function __construct(
        private readonly GroupService $groupService,
        private readonly MarkerService $markerService,
        private readonly ModelFactory $modelFactory,
        private readonly PestRepositoryInterface $pestRepository
    ) {
    }

    /**
     * @param int $groupId
     * @return PestModel[]
     */
    public function getPestsForDairy($groupId): array
    {
        return $this->pestRepository->getPestsForDairy($groupId);
    }

    /**
     * @param int $pestId
     * @return ?Pest
     */
    public function find(int $pestId): ?Pest
    {
        return $this->pestRepository->find($pestId);
    }

    /**
     * @return Pest[]
     */
    public function findAll(): array
    {
        return $this->pestRepository->findAll();
    }

    /**
     * @param int|null $groupId
     * @return PestModel[]
     */
    public function findAllByGroupId(?int $groupId = null): array
    {
        return $this->pestRepository->findAllByGroupId($groupId);
    }

    /**
     * @param string $title
     * @return PestModel[]
     */
    public function findPestsByTitle(string $title): array
    {
        return $this->pestRepository->findPestsByTitle($title);
    }

    /**
     * @param string $manufacturer
     * @return PestModel[]
     */
    public function findPestsByManufacturer(string $manufacturer): array
    {
        return $this->pestRepository->findPestsByManufacturer($manufacturer);
    }

    /**
     * @param DateTimeImmutable $date
     * @return PestModel[]
     */
    public function findPestsByUseDate(DateTimeImmutable $date): array
    {
        return $this->pestRepository->findPestsByUseDate($date);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return PestModel[]
     */
    public function getPestsPaginated(int $page, int $perPage): array
    {
        return $this->pestRepository->getPestsPaginated($page, $perPage);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param int|null $groupId
     * @return PestModel[]
     */
    public function getPestsPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array
    {
        return $this->pestRepository->getPestsPaginatedByGroupId($page, $perPage, $groupId);
    }

    /**
     * @param CreatePestModel $createPestModel
     * @return PestModel
     */
    public function create(CreatePestModel $createPestModel): PestModel
    {
        $group = $this->groupService->find($createPestModel->groupId);
        $marker = $this->markerService->find($createPestModel->markerId);

        $pest = new Pest(
            $group,
            $marker,
            $createPestModel->title,
            new PreparationVolume(
                $createPestModel->amount,
                $createPestModel->applicationRate
            ),
            new PreparationDetails(
                $createPestModel->manufacturer,
                $createPestModel->description,
                $createPestModel->comment
            )
        );

        $this->pestRepository->create($pest);

        return $this->pestRepository->toModel($pest);
    }

    /**
     * @param CreatePestDTO $dto
     * @return PestModel
     */
    public function createFromCreatePestDTO(CreatePestDTO $dto): PestModel
    {
        $model = $this->modelFactory->makeModel(
            CreatePestModel::class,
            $dto->groupId,
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
     * @param Pest $pest
     * @param UpdatePestModel $updatePestModel
     * @return PestModel
     */
    public function update(Pest $pest, UpdatePestModel $updatePestModel): PestModel
    {
        $group = $this->groupService->find($updatePestModel->groupId);
        $marker = $this->markerService->find($updatePestModel->markerId);

        $pest
            ->moveToGroup($group)
            ->changeFieldsWithMarker(
            $marker,
            $updatePestModel->title,
            new PreparationVolume(
                $updatePestModel->amount,
                $updatePestModel->applicationRate
            ),
            new PreparationDetails(
                $updatePestModel->manufacturer,
                $updatePestModel->description,
                $updatePestModel->comment
            )
        );

        $this->pestRepository->update();

        return $this->pestRepository->toModel($pest);
    }

    /**
     * @param Pest $pest
     * @param EditPestDTO $dto
     * @return PestModel
     */
    public function updateFromEditPestDTO(Pest $pest, EditPestDTO $dto): PestModel
    {
        $model = $this->modelFactory->makeModel(
            UpdatePestModel::class,
            $dto->groupId,
            $dto->markerId,
            $dto->title,
            $dto->amount,
            $dto->applicationRate,
            $dto->manufacturer,
            $dto->description,
            $dto->comment
        );

        return $this->update($pest, $model);
    }

    /**
     * @param int $pestId
     * @return void
     */
    public function removeById(int $pestId): void
    {
        $pest = $this->pestRepository->find($pestId);
        if ($pest !== null) {
            $this->pestRepository->remove($pest);
        }
    }

    /**
     * @param Pest $pest
     * @return void
     */
    public function removePest(Pest $pest): void
    {
        $this->pestRepository->remove($pest);
    }
}
