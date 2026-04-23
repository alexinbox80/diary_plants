<?php

namespace Unit\Domain\Service;

use DateTimeImmutable;
use App\Domain\Entity\Plant;
use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Offspring;
use App\Domain\Service\GroupService;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\PlantService;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Service\OffspringService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Model\Offspring\OffspringModel;
use App\Domain\Model\Offspring\CreateOffspringModel;
use App\Domain\Model\Offspring\UpdateOffspringModel;
use App\Domain\Repository\OffspringRepositoryInterface;

#[CoversClass(OffspringService::class)]
class OffspringServiceTest extends TestCase
{
    private PlantService|MockObject $plantService;
    private OffspringRepositoryInterface|MockObject $repository;
    private ModelFactory|MockObject $modelFactory;
    private GroupService|MockObject $groupService;
    private OffspringService $service;

    protected function setUp(): void
    {
        $this->plantService = $this->createMock(PlantService::class);
        $this->repository = $this->createMock(OffspringRepositoryInterface::class);
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->groupService = $this->createMock(GroupService::class);

        $this->service = new OffspringService(
            $this->plantService,
            $this->repository,
            $this->modelFactory,
            $this->groupService
        );
    }

    #[Test]
    public function testCreateSuccess(): void
    {
        // 1. Данные для создания
        $model = new CreateOffspringModel(
            groupId: 2,
            plantId: 10,
            fruitingDate: new DateTimeImmutable('2024-08-01'),
            floweringDate: new DateTimeImmutable('2024-05-15'),
            mass: 150,
            color: 'Красный',
            flavor: 'Сладкий',
            quantity: 5,
            comment: 'Первый урожай'
        );

        $group = $this->createMock(Group::class);
        $plant = $this->createMock(Plant::class);
        $offspringModel = $this->createMock(OffspringModel::class);

        // 2. Настройка моков
        $this->groupService->method('find')->with(2)->willReturn($group);
        $this->plantService->method('find')->with(10)->willReturn($plant);

        // Проверяем, что созданная сущность передается в репозиторий
        $this->repository->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(Offspring::class));

        $this->repository->method('toModel')->willReturn($offspringModel);

        // 3. Запуск
        $result = $this->service->create($model);

        // 4. Проверка результата
        $this->assertSame($offspringModel, $result);
    }

    #[Test]
    public function testUpdateSuccess(): void
    {
        $offspringEntity = $this->createMock(Offspring::class);
        $updateModel = new UpdateOffspringModel(
            groupId: 3,
            plantId: 15,
            fruitingDate: new DateTimeImmutable('2024-09-01'),
            floweringDate: new DateTimeImmutable('2024-06-01'),
            mass: 200,
            color: 'Желтый',
            flavor: 'Кислый',
            quantity: 10,
            comment: 'Обновлено'
        );

        $this->groupService->method('find')->willReturn($this->createMock(Group::class));
        $this->plantService->method('find')->willReturn($this->createMock(Plant::class));

        // Проверяем Fluent Interface (цепочку вызовов в сущности)
        $offspringEntity->expects($this->once())->method('moveToGroup')->willReturn($offspringEntity);
        $offspringEntity->expects($this->once())->method('moveToPlant')->willReturn($offspringEntity);
        $offspringEntity->expects($this->once())->method('recordResult');

        $this->repository->expects($this->once())->method('update');

        $this->service->update($offspringEntity, $updateModel);
    }

    #[Test]
    public function testRemoveById(): void
    {
        $id = 1;
        $offspring = $this->createMock(Offspring::class);

        $this->repository->method('find')->with($id)->willReturn($offspring);
        $this->repository->expects($this->once())->method('remove')->with($offspring);

        $this->service->removeById($id);
    }
}
