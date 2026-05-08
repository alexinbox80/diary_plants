<?php

namespace App\Domain\Service;

use DateTimeImmutable;
use App\Domain\Entity\Repotting;
use Psr\Cache\InvalidArgumentException;
use App\Domain\Model\Repotting\RepottingModel;
use App\Domain\Model\Repotting\CreateRepottingModel;
use App\Domain\Model\Repotting\UpdateRepottingModel;
use App\Domain\ValueObject\Enum\Repotting\PotMaterial;
use App\Domain\ValueObject\Repotting\RepottingDetails;
use App\Domain\Repository\RepottingRepositoryInterface;
use App\Domain\ValueObject\Enum\Repotting\RepottingType;
use App\Controller\Web\Dashboard\Repotting\EditRepotting\Input\EditRepottingDTO;
use App\Controller\Web\Dashboard\Repotting\CreateRepotting\Input\CreateRepottingDTO;

class RepottingService
{
    public function __construct(
        private readonly GroupService $groupService,
        private readonly PlantService $plantService,
        private readonly RepottingRepositoryInterface $repottingRepository,
        private readonly ModelFactory $modelFactory,
    ) {
    }

    /**
     * @param int $repottingId
     * @return ?Repotting
     */
    public function find(int $repottingId): ?Repotting
    {
        return $this->repottingRepository->find($repottingId);
    }

    /**
     * @return RepottingModel[]
     */
    public function findAll(): array
    {
        return $this->repottingRepository->findAll();
    }

    /**
     * @param ?int $groupId
     * @return RepottingModel[]
     */
    public function findAllByGroupId(?int $groupId = null): array
    {
        return $this->repottingRepository->findAllByGroupId($groupId);
    }

    /**
     * @param DateTimeImmutable $repottedAt
     * @return RepottingModel[]
     */
    public function findMarkersByLetter(DateTimeImmutable $repottedAt): array
    {
        return $this->repottingRepository->findRepottingsByRepottedAt($repottedAt);
    }

    /**
     * @return RepottingModel[]
     * @throws InvalidArgumentException
     */
    public function getRepottingsPaginated(int $page, int $perPage): array
    {
        return $this->repottingRepository->getRepottingsPaginated($page, $perPage);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param int|null $groupId
     * @return RepottingModel[]
     */
    public function getRepottingsPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array
    {
        return $this->repottingRepository->getRepottingsPaginatedByGroupId($page, $perPage, $groupId);
    }

    /**
     * @param CreateRepottingModel $createRepottingModel
     * @return RepottingModel
     * @throws InvalidArgumentException
     */
    public function create(CreateRepottingModel $createRepottingModel): RepottingModel
    {
        $group = $this->groupService->find($createRepottingModel->groupId);
        $plant = $this->plantService->find($createRepottingModel->plantId);

        $repotting = new Repotting(
            $group,
            $plant,
            $createRepottingModel->repottedAt,
            new RepottingDetails(
                RepottingType::from($createRepottingModel->type),
                PotMaterial::from($createRepottingModel->potMaterial),
                $createRepottingModel->potSize
            )
        );

        $repotting->setComment($createRepottingModel->comment);

        $this->repottingRepository->create($repotting);

        return $this->repottingRepository->toModel($repotting);
    }

    /**
     * @param CreateRepottingDTO $dto
     * @return RepottingModel
     * @throws InvalidArgumentException
     */
    public function createFromCreateRepottingDTO(CreateRepottingDTO $dto): RepottingModel
    {
        $model = $this->modelFactory->makeModel(
            CreateRepottingModel::class,
            $dto->groupId,
            $dto->plantId,
            $dto->repottedAt,
            $dto->type,
            $dto->potMaterial,
            $dto->potSize,
            $dto->comment
        );

        return $this->create($model);
    }

    /**
     * @param Repotting $repotting
     * @param UpdateRepottingModel $updateRepottingModel
     * @return RepottingModel
     */
    public function update(Repotting $repotting, UpdateRepottingModel $updateRepottingModel): RepottingModel
    {
        $group = $this->groupService->find($updateRepottingModel->groupId);
        $plant = $this->plantService->find($updateRepottingModel->plantId);

        $repotting->changeFields(
            $group,
            $plant,
            $updateRepottingModel->repottedAt,
            new RepottingDetails(
                RepottingType::from($updateRepottingModel->type),
                PotMaterial::from($updateRepottingModel->potMaterial),
                $updateRepottingModel->potSize
            )
        );

        $repotting->setComment($updateRepottingModel->comment);

        $this->repottingRepository->update();

        return $this->repottingRepository->toModel($repotting);
    }

    /**
     * @param Repotting $repotting
     * @param EditRepottingDTO $dto
     * @return RepottingModel
     */
    public function updateFromEditRepottingDTO(Repotting $repotting, EditRepottingDTO $dto): RepottingModel
    {
        $model = $this->modelFactory->makeModel(
            UpdateRepottingModel::class,
            $dto->groupId,
            $dto->plantId,
            $dto->repottedAt,
            $dto->type,
            $dto->potMaterial,
            $dto->potSize,
            $dto->comment
        );

        return $this->update($repotting, $model);
    }

    /**
     * @param int $repottingId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $repottingId): void
    {
        $repotting = $this->repottingRepository->find($repottingId);
        if ($repotting !== null) {
            $this->repottingRepository->remove($repotting);
        }
    }

    /**
     * @param Repotting $repotting
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeRepotting(Repotting $repotting): void
    {
        $this->repottingRepository->remove($repotting);
    }
}
