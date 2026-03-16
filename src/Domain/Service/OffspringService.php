<?php

namespace App\Domain\Service;

use App\Domain\Entity\Offspring;
use Psr\Cache\InvalidArgumentException;
use App\Domain\Model\Offspring\OffspringModel;
use App\Domain\ValueObject\Offspring\Phenology;
use App\Domain\ValueObject\Offspring\FruitMetrics;
use App\Domain\Model\Offspring\CreateOffspringModel;
use App\Domain\Model\Offspring\UpdateOffspringModel;
use App\Domain\Repository\OffspringRepositoryInterface;
use App\Controller\Web\Dashboard\Offspring\EditOffspring\Input\EditOffspringDTO;
use App\Controller\Web\Dashboard\Offspring\CreateOffspring\Input\CreateOffspringDTO;

class OffspringService
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly OffspringRepositoryInterface $offspringRepository,
        private readonly ModelFactory $modelFactory,
        private readonly GroupService $groupService,
    ) {
    }

    /**
     * @param int $offspringId
     * @return ?Offspring
     */
    public function find(int $offspringId): ?Offspring
    {
        return $this->offspringRepository->find($offspringId);
    }

    /**
     * @param int $offspringId
     * @return ?OffspringModel
     */
    public function findModel(int $offspringId): ?OffspringModel
    {
        return $this->offspringRepository->findModel($offspringId);
    }

    /**
     * @return OffspringModel[]
     */
    public function findAll(): array
    {
        return $this->offspringRepository->findAll();
    }

    /**
     * @return OffspringModel[]
     */
    public function findAllWithAttachments(): array
    {
        return $this->offspringRepository->findAllWithAttachments();
    }

    /**
     * @param string $mass
     * @return OffspringModel[]
     */
    public function findStatusesByMass(string $mass): array
    {
        return $this->offspringRepository->findOffspringsByMass($mass);
    }

    /**
     * @param string $flavor
     * @return OffspringModel[]
     */
    public function findStatusesByFlavor(string $flavor): array
    {
        return $this->offspringRepository->findOffspringsByFlavor($flavor);
    }

    /**
     * @param string $color
     * @return OffspringModel[]
     */
    public function findStatusesByColor(string $color): array
    {
        return $this->offspringRepository->findOffspringsByColor($color);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return OffspringModel[]
     */
    public function getOffspringsPaginated(int $page, int $perPage): array
    {
        return $this->offspringRepository->getOffspringsPaginated($page, $perPage);
    }

    /**
     * @param CreateOffspringModel $createOffspringModel
     * @return OffspringModel
     * @throws InvalidArgumentException
     */
    public function create(CreateOffspringModel $createOffspringModel): OffspringModel
    {
        $group = $this->groupService->find($createOffspringModel->groupId);
        $plant = $this->plantService->find($createOffspringModel->plantId);

        $offspring = new Offspring(
            $group,
            $plant,
        );

        $offspring->recordResult(
            new Phenology(
                $createOffspringModel->fruitingDate,
                $createOffspringModel->floweringDate,
            ),
            new FruitMetrics(
                $createOffspringModel->mass,
                $createOffspringModel->quantity,
                $createOffspringModel->color,
                $createOffspringModel->flavor,
            ),
            $createOffspringModel->comment
        );

        $this->offspringRepository->create($offspring);

        return $this->offspringRepository->toModel($offspring);
    }

    /**
     * @param CreateOffspringDTO $dto
     * @return OffspringModel
     * @throws InvalidArgumentException
     */
    public function createFromCreateOffspringDTO(CreateOffspringDTO $dto): OffspringModel
    {
        $model = $this->modelFactory->makeModel(
            CreateOffspringModel::class,
            2,
            $dto->plantId,
            $dto->fruitingDate,
            $dto->floweringDate,
            (int) $dto->mass,
            $dto->color,
            $dto->flavor,
            (int) $dto->quantity,
            $dto->comment,
        );

        return $this->create($model);
    }

    /**
     * @param Offspring $offspring
     * @param UpdateOffspringModel $updateOffspringModel
     * @return OffspringModel
     * @throws InvalidArgumentException
     */
    public function update(Offspring $offspring, UpdateOffspringModel $updateOffspringModel): OffspringModel
    {
        $group = $this->groupService->find($updateOffspringModel->groupId);
        $plant = $this->plantService->find($updateOffspringModel->plantId);

        $offspring
            ->moveToGroup($group)
            ->moveToPlant($plant)
            ->recordResult(
                new Phenology(
                    $updateOffspringModel->fruitingDate,
                    $updateOffspringModel->floweringDate
                ),
                new FruitMetrics(
                    $updateOffspringModel->mass,
                    $updateOffspringModel->quantity,
                    $updateOffspringModel->color,
                    $updateOffspringModel->flavor
                ),
                $updateOffspringModel->comment
            );

        $this->offspringRepository->update();

        return $this->offspringRepository->toModel($offspring);
    }

    /**
     * @param Offspring $offspring
     * @param EditOffspringDTO $dto
     * @return void
     */
    public function updateFromEditOffspringDTO(Offspring $offspring, EditOffspringDTO $dto): void
    {
        // Создаём модель обновления
        $model = $this->modelFactory->makeModel(
            UpdateOffspringModel::class,
            2,
            $dto->plantId,
            $dto->fruitingDate,
            $dto->floweringDate,
            $dto->mass,
            $dto->color,
            $dto->flavor,
            $dto->quantity,
            $dto->comment
        );

        // Выполняем обновление
        $this->update($offspring, $model);
    }

    /**
     * @param int $offspringId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $offspringId): void
    {
        $offspring = $this->offspringRepository->find($offspringId);
        if ($offspring !== null) {
            $this->offspringRepository->remove($offspring);
        }
    }

    /**
     * @param Offspring $offspring
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeOffspring(Offspring $offspring): void
    {
        $this->offspringRepository->remove($offspring);
    }
}
