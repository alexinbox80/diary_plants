<?php

namespace Unit\Domain\Service;

use App\Domain\Entity\Group;
use App\Domain\Entity\Plant;
use App\Domain\Entity\Analytic;
use PHPUnit\Framework\TestCase;
use App\Domain\Service\GroupService;
use App\Domain\Service\PlantService;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Service\AnalyticService;
use App\Domain\Model\Analytic\AnalyticModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Exception\EntityNotFoundException;
use App\Domain\Model\Analytic\CreateAnalyticModel;
use App\Domain\ValueObject\Analytic\IntervalMetrics;
use App\Domain\Repository\AnalyticRepositoryInterface;

#[CoversClass(AnalyticService::class)]
class AnalyticServiceTest extends TestCase
{
    private GroupService|MockObject $groupService;
    private PlantService|MockObject $plantService;
    private AnalyticRepositoryInterface|MockObject $repository;
    private AnalyticService $service;

    protected function setUp(): void
    {
        $this->groupService = $this->createMock(GroupService::class);
        $this->plantService = $this->createMock(PlantService::class);
        $this->repository = $this->createMock(AnalyticRepositoryInterface::class);

        $this->service = new AnalyticService(
            $this->groupService,
            $this->plantService,
            $this->repository
        );
    }

    #[Test]
    public function testCreateSuccess(): void
    {
        $model = new CreateAnalyticModel(plantId: 1, groupId: 10);

        $plant = $this->createMock(Plant::class);
        $group = $this->createMock(Group::class);
        $analyticModel = $this->createMock(AnalyticModel::class);

        $this->plantService->expects($this->once())->method('find')->with(1)->willReturn($plant);
        $this->groupService->expects($this->once())->method('find')->with(10)->willReturn($group);

        $this->repository->expects($this->once())->method('create')->with($this->isInstanceOf(Analytic::class));
        $this->repository->expects($this->once())->method('toModel')->willReturn($analyticModel);

        $result = $this->service->create($model);
        $this->assertSame($analyticModel, $result);
    }

    #[Test]
    public function testCreateThrowsExceptionIfPlantNotFound(): void
    {
        $model = new CreateAnalyticModel(1, 10);

        $this->plantService->method('find')->willReturn(null);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Group or Plant not found');

        $this->service->create($model);
    }

    #[Test]
    public function testUpdateWateringMetricsSuccess(): void
    {
        $plantId = 1;
        $metrics = new IntervalMetrics(5, 10.5);
        $analytic = $this->createMock(Analytic::class);
        $analyticModel = $this->createMock(AnalyticModel::class);

        $this->repository->expects($this->once())->method('findOneByPlantId')->with($plantId)->willReturn($analytic);

        // Проверяем, что в сущности вызывается метод смены метрик
        $analytic->expects($this->once())->method('changeWateringMetrics')->with($metrics);

        $this->repository->expects($this->once())->method('update');
        $this->repository->expects($this->once())->method('toModel')->with($analytic)->willReturn($analyticModel);

        $result = $this->service->updateWateringMetrics($plantId, $metrics);
        $this->assertSame($analyticModel, $result);
    }

    #[Test]
    public function testUpdateThrowsExceptionIfAnalyticNotFound(): void
    {
        $this->repository->method('findOneByPlantId')->willReturn(null);

        $this->expectException(EntityNotFoundException::class);

        $this->service->updateWateringMetrics(1, new IntervalMetrics(1, 1.0));
    }

    #[Test]
    public function testRemoveByPlantId(): void
    {
        $analytic = $this->createMock(Analytic::class);

        $this->repository->method('findOneByPlantId')->with(1)->willReturn($analytic);
        $this->repository->expects($this->once())->method('remove')->with($analytic);

        $this->service->removeByPlantId(1);
    }
}
