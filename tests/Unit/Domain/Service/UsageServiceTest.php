<?php

namespace Unit\Domain\Service;

use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Plant;
use App\Domain\Entity\Usage;
use PHPUnit\Framework\TestCase;
use App\Domain\Service\GroupService;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\PlantService;
use App\Domain\Service\UsageService;
use App\Domain\Model\Usage\UsageModel;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Event\UsageIsCreatedEvent;
use App\Domain\Event\UsagesBulkDeletedEvent;
use App\Domain\Model\Usage\CreateUsageModel;
use PHPUnit\Framework\MockObject\MockObject;
use App\Domain\Model\Usage\UpdateUsageModel;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Repository\UsageRepositoryInterface;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use App\Domain\Entity\Interfaces\GroupOwnedInterface;
use App\Domain\ValueObject\Usage\AttachableReference;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Controller\Web\Dashboard\Usage\EditUsage\Input\EditUsageDTO;

#[CoversClass(UsageService::class)]
class UsageServiceTest extends TestCase
{
    private PlantService|MockObject $plantService;
    private GroupService|MockObject $groupService;
    private UsageRepositoryInterface|MockObject $repository;
    private ModelFactory|MockObject $modelFactory;
    private EventDispatcherInterface|MockObject $eventDispatcher;
    private UsageService $service;

    protected function setUp(): void
    {
        $this->plantService = $this->createMock(PlantService::class);
        $this->groupService = $this->createMock(GroupService::class);
        $this->repository = $this->createMock(UsageRepositoryInterface::class);
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->service = new UsageService(
            $this->plantService,
            $this->groupService,
            $this->repository,
            $this->modelFactory,
            $this->eventDispatcher
        );
    }

    #[Test]
    public function testCreateSuccessDispatchesEvent(): void
    {
        $useDate = new DateTimeImmutable('2024-05-01');
        $model = new CreateUsageModel(
            groupId: 2,
            plantId: 10,
            useDate: $useDate,
            usableId: 50,
            usableType: AttachableType::WATERING->value,
            comment: 'Test usage'
        );

        $group = $this->createMock(Group::class);
        $plant = $this->createMock(Plant::class);
        $usageModel = $this->createMock(UsageModel::class);

        // Настраиваем UsageModel для события
        $usageModel->method('getId')->willReturn(1);
        $usageModel->method('getGroupId')->willReturn(2);
        $usageModel->method('getUseDate')->willReturn($useDate);
        $usageModel->method('getPlantId')->willReturn(10);
        $usageModel->method('getUsableId')->willReturn(50);
        $usageModel->method('getUsableType')->willReturn(AttachableType::WATERING->value);

        $this->groupService->method('find')->with(2)->willReturn($group);
        $this->plantService->method('find')->with(10)->willReturn($plant);

        $this->repository->expects($this->once())->method('create');
        $this->repository->method('toModel')->willReturn($usageModel);

        // ПРОВЕРКА: Событие должно быть отправлено
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(UsageIsCreatedEvent::class));

        $result = $this->service->create($model);

        $this->assertSame($usageModel, $result);
    }

    #[Test]
    public function testGetUsagesReturnsFormattedJsonArray(): void
    {
        $usageModel = $this->createMock(UsageModel::class);
        $usageModel->method('toJson')->willReturn(['id' => 1, 'type' => 'watering']);

        $this->repository->method('getUsages')->willReturn([$usageModel]);

        $result = $this->service->getUsages(2024, 5);

        $this->assertIsArray($result);
        $this->assertSame(['id' => 1, 'type' => 'watering'], $result[0]);
    }

    #[Test]
    public function testUpdateSuccess(): void
    {
        $usageEntity = $this->createMock(Usage::class);

        // --- Добавлено: настройка Target для избежания TypeError ---
        $target = $this->createMock(AttachableReference::class);
        $target->method('getUsableType')->willReturn(AttachableType::WATERING);
        $target->method('getUsableId')->willReturn(50);
        $usageEntity->method('getTarget')->willReturn($target);

        // Настраиваем репозиторий на возврат объекта (связь 1-к-1)
        $this->repository->method('findEntitiesByAttachable')
            ->willReturn($this->createMock(GroupOwnedInterface::class));
        // ----------------------------------------------------------

        $useDate = new DateTimeImmutable('2024-06-01');
        $model = new UpdateUsageModel(
            groupId: 2, plantId: 10, useDate: $useDate,
            usableId: 50, usableType: AttachableType::WATERING->value, comment: 'Upd'
        );

        $this->groupService->method('find')->willReturn($this->createMock(Group::class));
        $this->plantService->method('find')->willReturn($this->createMock(Plant::class));

        $usageEntity->method('moveToGroup')->willReturn($usageEntity);
        $usageEntity->method('setLoadedAttachment');
        $usageEntity->expects($this->once())->method('changeFields');

        $this->repository->expects($this->once())->method('update');
        $this->repository->method('toModel')->willReturn($this->createMock(UsageModel::class));

        $result = $this->service->update($usageEntity, $model);
        $this->assertInstanceOf(UsageModel::class, $result);
    }

    #[Test]
    public function testRemoveUsagesDispatchesBulkEvent(): void
    {
        $ids = [1, 2, 3];
        $affectedPlantIds = [10, 11];

        // 1. Ожидаем поиск ID растений перед удалением
        $this->repository->expects($this->once())
            ->method('findPlantIdsByIds')
            ->with($ids)
            ->willReturn($affectedPlantIds);

        // 2. Ожидаем массовое удаление в репозитории
        $this->repository->expects($this->once())
            ->method('removeByIds')
            ->with($ids, 2)
            ->willReturn(3);

        // 3. ПРОВЕРКА: должно быть отправлено событие массового удаления
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (UsagesBulkDeletedEvent $event) use ($affectedPlantIds) {
                return $event->plantIds === $affectedPlantIds
                    && $event->usableType === AttachableType::WATERING->value;
            }));

        $count = $this->service->removeUsages($ids);

        $this->assertSame(3, $count);
    }

    #[Test]
    public function testUpdateFromEditUsageDTOUsesModelFactory(): void
    {
        $usageEntity = $this->createMock(Usage::class);
        $useDate = new DateTimeImmutable('2024-05-10');

        // --- ДОБАВЬТЕ ЭТОТ БЛОК ---
        // 1. Настраиваем Target, чтобы getUsableType не возвращал null
        $target = $this->createMock(AttachableReference::class);
        $target->method('getUsableType')->willReturn(AttachableType::WATERING);
        $target->method('getUsableId')->willReturn(50);
        $usageEntity->method('getTarget')->willReturn($target);

        // 2. Настраиваем репозиторий для вызова findEntitiesByAttachable
        $this->repository->method('findEntitiesByAttachable')
            ->willReturn($this->createMock(GroupOwnedInterface::class));
        // ---------------------------

        $dto = new EditUsageDTO(
            groupId: 2,
            plantId: 10,
            useDate: $useDate,
            usableId: 50,
            usableType: 'watering',
            comment: 'DTO Comment'
        );

        $model = new UpdateUsageModel(
            groupId: 2,
            plantId: 10,
            useDate: $useDate,
            usableId: 50,
            usableType: 'watering',
            comment: 'DTO Comment'
        );

        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->willReturn($model);

        // Настраиваем остальные зависимости для внутреннего вызова update()
        $this->groupService->method('find')->willReturn($this->createMock(Group::class));
        $this->plantService->method('find')->willReturn($this->createMock(Plant::class));
        $this->repository->method('toModel')->willReturn($this->createMock(UsageModel::class));

        // Настраиваем Fluent Interface для сущности
        $usageEntity->method('moveToGroup')->willReturn($usageEntity);
        $usageEntity->method('setLoadedAttachment');

        $this->service->updateFromEditUsageDTO($usageEntity, $dto);
    }
}
