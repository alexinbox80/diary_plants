<?php

namespace Unit\Domain\Service;

use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use App\Domain\Entity\Watering;
use PHPUnit\Framework\TestCase;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\GroupService;
use App\Domain\Service\MarkerService;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Service\WateringService;
use PHPUnit\Framework\MockObject\MockObject;
use App\Domain\Model\Watering\WateringModel;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Model\Watering\CreateWateringModel;
use App\Domain\Model\Watering\UpdateWateringModel;
use App\Domain\ValueObject\Enum\Watering\WaterType;
use App\Domain\ValueObject\Watering\WateringDetails;
use App\Domain\Repository\WateringRepositoryInterface;
use App\Domain\ValueObject\Enum\Watering\WateringMethod;

#[CoversClass(WateringService::class)]
class WateringServiceTest extends TestCase
{
    private GroupService|MockObject $groupService;
    private MarkerService|MockObject $markerService;
    private ModelFactory|MockObject $modelFactory;
    private WateringRepositoryInterface|MockObject $repository;
    private WateringService $service;

    protected function setUp(): void
    {
        $this->groupService = $this->createMock(GroupService::class);
        $this->markerService = $this->createMock(MarkerService::class);
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->repository = $this->createMock(WateringRepositoryInterface::class);

        $this->service = new WateringService(
            $this->groupService,
            $this->markerService,
            $this->modelFactory,
            $this->repository
        );
    }

    #[Test]
    public function testCreateSuccess(): void
    {
        $model = new CreateWateringModel(
            groupId: 2,
            markerId: 10,
            amount: 500,
            waterType: WaterType::DISTILLED->value,
            wateringMethod: WateringMethod::BOTTOM->value,
            temperature: 25.5,
            description: 'Regular watering',
            comment: 'Morning'
        );

        $this->groupService->method('find')->with(2)->willReturn($this->createMock(Group::class));
        $this->markerService->method('find')->with(10)->willReturn($this->createMock(Marker::class));

        $this->repository->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(Watering::class));

        $this->repository->method('toModel')->willReturn($this->createMock(WateringModel::class));

        $result = $this->service->create($model);

        $this->assertInstanceOf(WateringModel::class, $result);
    }

    #[Test]
    public function testUpdateSuccess(): void
    {
        // 1. Создаем мок сущности Watering
        $wateringEntity = $this->createMock(Watering::class);

        $model = new UpdateWateringModel(
            groupId: 2,
            markerId: 10,
            amount: 1000,
            waterType: WaterType::TAP->value,
            wateringMethod: WateringMethod::IMMERSION->value,
            temperature: 22.0,
            description: 'Updated desc',
            comment: 'Updated comment'
        );

        $group = $this->createMock(Group::class);
        $marker = $this->createMock(Marker::class);

        $this->groupService->method('find')->with(2)->willReturn($group);
        $this->markerService->method('find')->with(10)->willReturn($marker);

        // 2. Настройка Fluent Interface для moveToGroup и changeFields
        $wateringEntity->method('moveToGroup')->with($group)->willReturn($wateringEntity);
        $wateringEntity->method('changeFields')->willReturn($wateringEntity);

        // Настройка обычных сеттеров (они возвращают $this/self)
        $wateringEntity->method('setDescription')->willReturn($wateringEntity);
        $wateringEntity->method('setComment')->willReturn($wateringEntity);

        // 3. Проверяем, что сервис вызовет правильные методы бизнес-логики в сущности
        $wateringEntity->expects($this->once())->method('moveToGroup')->with($group);
        $wateringEntity->expects($this->once())
            ->method('changeFields')
            ->with(
                $marker,
                $this->isInstanceOf(WateringDetails::class)
            );

        $wateringEntity->expects($this->once())->method('setDescription')->with('Updated desc');
        $wateringEntity->expects($this->once())->method('setComment')->with('Updated comment');

        // 4. Ожидания репозитория
        $this->repository->expects($this->once())->method('update');

        // 5. Выполнение
        $this->service->update($wateringEntity, $model);
    }

    #[Test]
    public function testRemoveById(): void
    {
        $entity = $this->createMock(Watering::class);
        $this->repository->method('find')->with(1)->willReturn($entity);
        $this->repository->expects($this->once())->method('remove')->with($entity);

        $this->service->removeById(1);
    }
}
