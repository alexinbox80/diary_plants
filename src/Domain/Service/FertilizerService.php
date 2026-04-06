<?php

namespace App\Domain\Service;

use DateTimeImmutable;
use App\Domain\Entity\Fertilizer;
use Psr\Cache\InvalidArgumentException;
use App\Domain\Model\Fertilizer\FertilizerModel;
use App\Domain\Model\Fertilizer\CreateFertilizerModel;
use App\Domain\Model\Fertilizer\UpdateFertilizerModel;
use App\Domain\Repository\FertilizerRepositoryInterface;
use App\Domain\ValueObject\Preparation\PreparationVolume;
use App\Domain\ValueObject\Preparation\PreparationDetails;
use App\Controller\Web\Dashboard\Fertilizer\EditFertilizer\Input\EditFertilizerDTO;
use App\Controller\Web\Dashboard\Fertilizer\CreateFertilizer\Input\CreateFertilizerDTO;

class FertilizerService
{
    public function __construct(
        private readonly GroupService $groupService,
        private readonly MarkerService $markerService,
        private readonly ModelFactory $modelFactory,
        private readonly FertilizerRepositoryInterface $fertilizerRepository
    ) {
    }

    /**
     * @param int $groupId
     * @return FertilizerModel[]
     */
    public function getFertilizersForDairy($groupId): array
    {
        return $this->fertilizerRepository->getFertilizersForDairy($groupId);
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
     * @param DateTimeImmutable $date
     * @return FertilizerModel[]
     */
    public function findFertilizersByUseDate(DateTimeImmutable $date): array
    {
        return $this->fertilizerRepository->findFertilizersByUseDate($date);
    }

    /**
     * @return FertilizerModel[]
     * @throws InvalidArgumentException
     */
    public function getFertilizersPaginated(int $page, int $perPage): array
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
        $group = $this->groupService->find($createFertilizerModel->groupId);
        $marker = $this->markerService->find($createFertilizerModel->markerId);

        $fertilizer = new Fertilizer(
            $group,
            $marker,
            $createFertilizerModel->title,
            new PreparationVolume(
                $createFertilizerModel->amount,
                $createFertilizerModel->applicationRate
            ),
            new PreparationDetails(
                $createFertilizerModel->manufacturer,
                $createFertilizerModel->description,
                $createFertilizerModel->comment
            )
        );

        $this->fertilizerRepository->create($fertilizer);

        return $this->fertilizerRepository->toModel($fertilizer);
    }

    /**
     * @param CreateFertilizerDTO $dto
     * @return FertilizerModel
     */
    public function createFromCreateFertilizerDTO(CreateFertilizerDTO $dto): FertilizerModel
    {
        $model = $this->modelFactory->makeModel(
            CreateFertilizerModel::class,
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
     * @param Fertilizer $fertilizer
     * @param UpdateFertilizerModel $updateFertilizerModel
     * @return FertilizerModel
     * @throws InvalidArgumentException
     */
    public function update(Fertilizer $fertilizer, UpdateFertilizerModel $updateFertilizerModel): FertilizerModel
    {
        $group = $this->groupService->find($updateFertilizerModel->groupId);
        $marker = $this->markerService->find($updateFertilizerModel->markerId);

        $fertilizer->changeFieldsWithMarker(
            $group,
            $marker,
            $updateFertilizerModel->title,
            new PreparationVolume(
                $updateFertilizerModel->amount,
                $updateFertilizerModel->applicationRate
            ),
            new PreparationDetails(
                $updateFertilizerModel->manufacturer,
                $updateFertilizerModel->description,
                $updateFertilizerModel->comment
            )
        );

        $this->fertilizerRepository->update();

        return $this->fertilizerRepository->toModel($fertilizer);
    }

    /**
     * @param Fertilizer $fertilizer
     * @param EditFertilizerDTO $dto
     * @return FertilizerModel
     */
    public function updateFromEditFertilizerDTO(Fertilizer $fertilizer, EditFertilizerDTO $dto): FertilizerModel
    {
        $model = $this->modelFactory->makeModel(
            UpdateFertilizerModel::class,
            2,
            $dto->markerId,
            $dto->title,
            $dto->amount,
            $dto->applicationRate,
            $dto->manufacturer,
            $dto->description,
            $dto->comment
        );

        return $this->update($fertilizer, $model);
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
