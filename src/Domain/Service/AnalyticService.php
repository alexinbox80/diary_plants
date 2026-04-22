<?php

namespace App\Domain\Service;

use App\Domain\Entity\Analytic;
use App\Domain\Model\Analytic\AnalyticModel;
use App\Domain\Exception\EntityNotFoundException;
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
    ) {
    }

    /**
     * @param int $analyticId
     * @return AnalyticModel|null
     */
    public function findModel(int $analyticId): ?AnalyticModel
    {
        $analytic = $this->analyticRepository->find($analyticId);
        return $analytic ? $this->analyticRepository->toModel($analytic) : null;
    }

    /**
     * @param int $plantId
     * @return AnalyticModel|null
     */
    public function findModelByPlantId(int $plantId): ?AnalyticModel
    {
        $analytic = $this->analyticRepository->findOneByPlantId($plantId);
        return $analytic ? $this->analyticRepository->toModel($analytic) : null;
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return AnalyticModel[]
     */
    public function getAnalyticPaginated(int $page, int $perPage): array
    {
        return $this->analyticRepository->getAnalyticsPaginated($page, $perPage);
    }

    /**
     * @param CreateAnalyticModel $model
     * @return AnalyticModel
     */
    public function create(CreateAnalyticModel $model): AnalyticModel
    {
        $group = $this->groupService->find($model->groupId);
        $plant = $this->plantService->find($model->plantId);

        if (!$group || !$plant) {
            throw new EntityNotFoundException('Group or Plant not found for Analytic creation');
        }

        $analytic = new Analytic($plant, $group);
        $this->analyticRepository->create($analytic);

        return $this->analyticRepository->toModel($analytic);
    }

    /**
     * Универсальный метод обновления через DTO
     * @param int $plantId
     * @param UpdateAnalyticModel $model
     * @return AnalyticModel
     */
    public function update(int $plantId, UpdateAnalyticModel $model): AnalyticModel
    {
        $analytic = $this->analyticRepository->findOneByPlantId($plantId);

        if (!$analytic) {
            throw new EntityNotFoundException("Analytic for plant $plantId not found");
        }

        $analytic->changeWateringMetrics(
            new IntervalMetrics($model->count, $model->averageDays)
        );

        $this->analyticRepository->update(); // Flush

        return $this->analyticRepository->toModel($analytic);
    }

    /**
     * Упрощенный метод специально для метрик (используется в вашей команде)
     * @param int $plantId
     * @param IntervalMetrics $metrics
     * @return AnalyticModel
     */
    public function updateWateringMetrics(int $plantId, IntervalMetrics $metrics): AnalyticModel
    {
        $analytic = $this->analyticRepository->findOneByPlantId($plantId);

        if (!$analytic) {
            throw new EntityNotFoundException("Analytic for plant $plantId not found");
        }

        $analytic->changeWateringMetrics($metrics);
        $this->analyticRepository->update();

        return $this->analyticRepository->toModel($analytic);
    }

    /**
     * @param int $plantId
     * @return void
     */
    public function removeByPlantId(int $plantId): void
    {
        $analytic = $this->analyticRepository->findOneByPlantId($plantId);
        if ($analytic) {
            $this->analyticRepository->remove($analytic);
        }
    }

    /**
     * Внутренний метод для получения Entity, если он нужен другим сервисам
     * @param int $plantId
     * @return Analytic|null
     */
    public function getEntityByPlantId(int $plantId): ?Analytic
    {
        return $this->analyticRepository->findOneByPlantId($plantId);
    }
}
