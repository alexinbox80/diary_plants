<?php

namespace App\Domain\Service;

use App\Domain\Entity\Analytic;
use Psr\Cache\InvalidArgumentException;
use App\Domain\Model\Analytic\AnalyticModel;
use App\Domain\Model\Analytic\CreateAnalyticModel;
use App\Domain\Model\Analytic\UpdateAnalyticModel;
use App\Domain\ValueObject\Analytic\IntervalMetrics;
use App\Domain\Repository\AnalyticRepositoryInterface;

class AnalyticService
{
    public function __construct(
        private readonly GroupService $groupService,
        private readonly PlantService $plantService,
        private readonly AnalyticRepositoryInterface $analyticRepository,
        private readonly ModelFactory $modelFactory,
    ) {
    }

    /**
     * @param int $analyticId
     * @return ?Analytic
     */
    public function find(int $analyticId): ?Analytic
    {
        return $this->analyticRepository->find($analyticId);
    }

    /**
     * @return Analytic[]
     */
    public function findAll(): array
    {
        return $this->analyticRepository->findAll();
    }

    /**
     * @param int $plantId
     * @return AnalyticModel[]
     */
    public function findAnalyticsByPlantId(int $plantId): array
    {
        return $this->analyticRepository->findAnalyticsByPlantId($plantId);
    }

    /**
     * @param int $groupId
     * @return AnalyticModel[]
     */
    public function findAnalyticsByGroupId(int $groupId): array
    {
        return $this->analyticRepository->findAnalyticsByGroupId($groupId);
    }

    /**
     * @return AnalyticModel[]
     * @throws InvalidArgumentException
     */
    public function getAnalyticPaginated(int $page, int $perPage): array
    {
        return $this->analyticRepository->getAnalyticsPaginated($page, $perPage);
    }

    /**
     * @param CreateAnalyticModel $createAnalyticModel
     * @return AnalyticModel
     * @throws InvalidArgumentException
     */
    public function create(CreateAnalyticModel $createAnalyticModel): AnalyticModel
    {
        $group = $this->groupService->find($createAnalyticModel->groupId);
        $plant = $this->plantService->find($createAnalyticModel->plantId);

        if (!$group || !$plant) {
            throw new \Exception('Group or Plant not found for Analytic creation');
        }

        $analytic = new Analytic($plant, $group);
        $this->analyticRepository->create($analytic);

        return $this->analyticRepository->toModel($analytic);
    }

    /**
     * @param Analytic $analytic
     * @param UpdateAnalyticModel $updateAnalyticModel
     * @return AnalyticModel
     * @throws InvalidArgumentException
     */
    public function update(Analytic $analytic, UpdateAnalyticModel $updateAnalyticModel): AnalyticModel
    {
        $analytic->changeWateringMetrics(
            new IntervalMetrics(
                $updateAnalyticModel->count,
                $updateAnalyticModel->averageDays,
            )
        );

        $this->analyticRepository->update();

        return $this->analyticRepository->toModel($analytic);
    }

    /**
     * @param int $plantId
     * @return Analytic|null
     */
    public function findEntityByPlantId(int $plantId): ?Analytic
    {
        return $this->analyticRepository->findOneByPlantId($plantId);
    }

    /**
     * @param int $plantId
     * @param IntervalMetrics $metrics
     * @return AnalyticModel
     * @throws InvalidArgumentException
     */
    public function updateWateringMetrics(int $plantId, IntervalMetrics $metrics): AnalyticModel
    {
        $analytic = $this->findEntityByPlantId($plantId);

        if (null === $analytic) {
            throw new \Exception("Analytic entity for plant $plantId not found");
        }

        /** @var UpdateAnalyticModel $updateModel */
        $updateModel = $this->modelFactory->makeModel(
            UpdateAnalyticModel::class,
            $analytic->getGroup()->getId(),
            $plantId,
            $metrics->getCount(),
            $metrics->getAverageDays()
        );

        return $this->update($analytic, $updateModel);
    }

    /**
     * @param int $analyticId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $analyticId): void
    {
        $analytic = $this->analyticRepository->find($analyticId);
        if ($analytic !== null) {
            $this->analyticRepository->remove($analytic);
        }
    }

    /**
     * @param Analytic $analytic
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeAnalytic(Analytic $analytic): void
    {
        $this->analyticRepository->remove($analytic);
    }
}
