<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Analytic;
use App\Domain\Model\Analytic\AnalyticModel;
use App\Domain\Repository\AnalyticRepositoryInterface;

class AnalyticRepositoryDecorator implements AnalyticRepositoryInterface
{
    public function __construct(
        private readonly AnalyticRepository $analyticRepository
    ) {
    }

    /**
     * @return AnalyticModel[]
     * @throws \Exception
     */
    public function getAnalyticsPaginated(int $page, int $perPage): array
    {
        $analyticsPaginated = $this->analyticRepository->getAnalyticsPaginated($page, $perPage);

        if (!is_array($analyticsPaginated['items'])) {
            throw new \InvalidArgumentException('Expected array for Analytics');
        }

        $analyticsModel = array_map(
            fn (Analytic $analytic): AnalyticModel => $this->toModel($analytic),
            $analyticsPaginated['items']
        );

        return [
            'analyticsModel' => $analyticsModel,
            'pagination' => $analyticsPaginated['pagination']
        ];
    }

    /**
     * @param int $analyticId
     * @return Analytic|null
     */
    public function find(int $analyticId): ?Analytic
    {
        return $this->analyticRepository->find($analyticId);
    }

    /**
     * @param int $analyticId
     * @return AnalyticModel|null
     */
    public function findModel(int $analyticId): ?AnalyticModel
    {
        $analytic = $this->analyticRepository->find($analyticId);

        return $this->toModel($analytic);
    }

    /**
     * @return AnalyticModel[]
     */
    public function findAll(): array
    {
        $analytics = $this->analyticRepository->findAll();

        return array_map(
            fn (Analytic $analytic): AnalyticModel => $this->toModel($analytic),
            $analytics
        );
    }

    /**
     * @param int $plantId
     * @return AnalyticModel[]
     */
    public function findAnalyticsByPlantId(int $plantId): array
    {
        $analytics = $this->analyticRepository->findAnalyticsByPlantId($plantId);

        return array_map(
            fn (Analytic $analytic): AnalyticModel => $this->toModel($analytic),
            $analytics
        );
    }

    /**
     * @param int $plantId
     * @return AnalyticModel[]
     */
    public function findAnalyticsByGroupId(int $plantId): array
    {
        $analytics = $this->analyticRepository->findAnalyticsByGroupId($plantId);

        return array_map(
            fn (Analytic $analytic): AnalyticModel => $this->toModel($analytic),
            $analytics
        );
    }

    /**
     * @param int $plantId
     * @return Analytic|null
     */
    public function findOneByPlantId(int $plantId): ?Analytic
    {
        return $this->analyticRepository->findOneByPlantId($plantId);
    }

    /**
     * @param Analytic $analytic
     * @return int
     */
    public function create(Analytic $analytic): int
    {
        return $this->analyticRepository->create($analytic);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->analyticRepository->update();
    }

    /**
     * @param Analytic $analytic
     * @return void
     */
    public function remove(Analytic $analytic): void
    {
        $this->analyticRepository->remove($analytic);
    }

    /**
     * @param Analytic $analytic
     * @return AnalyticModel
     */
    public function toModel(Analytic $analytic): AnalyticModel
    {
        return AnalyticModel::fromEntity($analytic);
    }
}
