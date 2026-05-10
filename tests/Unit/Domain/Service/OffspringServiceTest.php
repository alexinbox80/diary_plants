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
use App\Domain\Repository\AttachmentRepositoryInterface;

#[CoversClass(OffspringService::class)]
class OffspringServiceTest extends TestCase
{
    private PlantService|MockObject $plantService;
    private OffspringRepositoryInterface|MockObject $repository;
    private ModelFactory|MockObject $modelFactory;
    private GroupService|MockObject $groupService;
    private AttachmentRepositoryInterface|MockObject $attachmentRepository;
    private OffspringService $service;

    protected function setUp(): void
    {
        $this->plantService = $this->createMock(PlantService::class);
        $this->repository = $this->createMock(OffspringRepositoryInterface::class);
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->groupService = $this->createMock(GroupService::class);
        $this->attachmentRepository = $this->createMock(AttachmentRepositoryInterface::class);

        $this->service = new OffspringService(
            $this->plantService,
            $this->repository,
            $this->modelFactory,
            $this->groupService,
            $this->attachmentRepository
        );
    }

    #[Test]
    public function testCreateSuccess(): void
    {
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

        // Даем мокам разные ID, чтобы сработала логика смены группы/растения
        $group = $this->createMock(Group::class);
        $group->method('getId')->willReturn(2);

        $plant = $this->createMock(Plant::class);
        $plant->method('getId')->willReturn(10);

        $offspringModel = $this->createMock(OffspringModel::class);

        $this->groupService->method('find')->with(2)->willReturn($group);
        $this->plantService->method('find')->with(10)->willReturn($plant);

        $this->repository->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(Offspring::class));

        $this->repository->method('toModel')->willReturn($offspringModel);

        $result = $this->service->create($model);

        $this->assertSame($offspringModel, $result);
    }

    #[Test]
    public function testUpdateSuccess(): void
    {
        $offspringEntity = $this->createMock(Offspring::class);
        $offspringEntity->method('getId')->willReturn(1);

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

        // Настройка моков для update (нужны для поиска вложений)
        $this->groupService->method('find')->willReturn($this->createMock(Group::class));
        $this->plantService->method('find')->willReturn($this->createMock(Plant::class));
        $this->attachmentRepository->method('findEntitiesByAttachable')->willReturn([]);

        // Настраиваем цепочку вызовов (Fluent Interface)
        $offspringEntity->method('moveToGroup')->willReturn($offspringEntity);
        $offspringEntity->method('moveToPlant')->willReturn($offspringEntity);
        // Метод recordResult, судя по всему, возвращает void или self, настроим на возврат
        $offspringEntity->method('recordResult');
        $offspringEntity->method('setLoadedAttachments');

        $this->repository->expects($this->once())->method('update');
        $this->repository->method('toModel')->willReturn($this->createMock(OffspringModel::class));

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
