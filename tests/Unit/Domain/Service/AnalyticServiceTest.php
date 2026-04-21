<?php

namespace Unit\Domain\Service;

use App\Domain\Entity\Group;
use App\Domain\Entity\Plant;
use App\Domain\Entity\Analytic;
use PHPUnit\Framework\TestCase;
use App\Domain\Service\GroupService;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\PlantService;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Service\AnalyticService;
use App\Domain\Model\Analytic\AnalyticModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Model\Analytic\CreateAnalyticModel;
use App\Domain\Model\Analytic\UpdateAnalyticModel;
use App\Domain\ValueObject\Analytic\IntervalMetrics;
use App\Domain\Repository\AnalyticRepositoryInterface;

#[CoversClass(AnalyticService::class)]
class AnalyticServiceTest extends TestCase
{
    private GroupService&MockObject $groupService;
    private PlantService&MockObject $plantService;
    private AnalyticRepositoryInterface&MockObject $analyticRepository;
    private ModelFactory&MockObject $modelFactory;
    private AnalyticService $service;

    protected function setUp(): void
    {
        $this->groupService = $this->createMock(GroupService::class);
        $this->plantService = $this->createMock(PlantService::class);
        $this->analyticRepository = $this->createMock(AnalyticRepositoryInterface::class);
        $this->modelFactory = $this->createMock(ModelFactory::class);

        $this->service = new AnalyticService(
            $this->groupService,
            $this->plantService,
            $this->analyticRepository,
            $this->modelFactory
        );
    }

    #[Test]
    public function createSuccessfullyCreatesAnalytic(): void
    {
        // 1. Arrange
        $createModel = new CreateAnalyticModel(groupId: 1, plantId: 100);

        $group = $this->createMock(Group::class);
        $plant = $this->createMock(Plant::class);
        $analyticModel = $this->createMock(AnalyticModel::class);

        $this->groupService->method('find')->with(1)->willReturn($group);
        $this->plantService->method('find')->with(100)->willReturn($plant);

        // Проверяем, что репозиторий вызвал метод сохранения
        $this->analyticRepository->expects($this->once())->method('create');
        $this->analyticRepository->method('toModel')->willReturn($analyticModel);

        // 2. Act
        $result = $this->service->create($createModel);

        // 3. Assert
        $this->assertSame($analyticModel, $result);
    }

    #[Test]
    public function createThrowsExceptionIfPlantNotFound(): void
    {
        $createModel = new CreateAnalyticModel(1, 100);
        $this->groupService->method('find')->willReturn($this->createMock(Group::class));
        $this->plantService->method('find')->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Group or Plant not found for Analytic creation');

        $this->service->create($createModel);
    }

    #[Test]
    public function updateWateringMetricsUpdatesExistingAnalytic(): void
    {
        // 1. Arrange
        $plantId = 100;
        $groupId = 1;
        $metrics = new IntervalMetrics(5, 10.5);

        $analytic = $this->createMock(Analytic::class);
        $group = $this->createMock(Group::class);
        $group->method('getId')->willReturn($groupId);
        $analytic->method('getGroup')->willReturn($group);

        $updateModel = new UpdateAnalyticModel(
            plantId: $plantId,
            groupId: $groupId,
            count: 5,
            averageDays: 10.5
        );

        $this->analyticRepository
            ->method('findOneByPlantId')
            ->with($plantId)
            ->willReturn($analytic);

        // Настраиваем фабрику, чтобы она возвращала наш подготовленный объект
        $this->modelFactory
            ->method('makeModel')
            ->willReturn($updateModel);

        // Ожидаем, что у сущности вызовут метод смены метрик
        $analytic->expects($this->once())->method('changeWateringMetrics');
        $this->analyticRepository->expects($this->once())->method('update');

        // 2. Act
        $this->service->updateWateringMetrics($plantId, $metrics);
    }

    #[Test]
    public function updateWateringMetricsThrowsExceptionIfAnalyticNotFound(): void
    {
        $this->analyticRepository->method('findOneByPlantId')->willReturn(null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Analytic entity for plant 100 not found");

        $this->service->updateWateringMetrics(100, new IntervalMetrics(0, 0));
    }
}
