<?php

namespace Unit\Domain\Service;

use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Fertilizer;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\GroupService;
use App\Domain\Service\MarkerService;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Service\FertilizerService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Model\Fertilizer\FertilizerModel;
use App\Domain\Model\Fertilizer\CreateFertilizerModel;
use App\Domain\Model\Fertilizer\UpdateFertilizerModel;
use App\Domain\Repository\FertilizerRepositoryInterface;
use App\Domain\ValueObject\Preparation\PreparationVolume;
use App\Domain\ValueObject\Preparation\PreparationDetails;

#[CoversClass(FertilizerService::class)]
class FertilizerServiceTest extends TestCase
{
    private GroupService|MockObject $groupService;
    private MarkerService|MockObject $markerService;
    private ModelFactory|MockObject $modelFactory;
    private FertilizerRepositoryInterface|MockObject $repository;
    private FertilizerService $service;

    protected function setUp(): void
    {
        $this->groupService = $this->createMock(GroupService::class);
        $this->markerService = $this->createMock(MarkerService::class);
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->repository = $this->createMock(FertilizerRepositoryInterface::class);

        $this->service = new FertilizerService(
            $this->groupService,
            $this->markerService,
            $this->modelFactory,
            $this->repository
        );
    }

    #[Test]
    public function testCreateSuccess(): void
    {
        // 1. Подготовка данных
        $model = new CreateFertilizerModel(
            groupId: 1,
            markerId: 10,
            title: 'Супер-Рост',
            amount: 500,
            manufacturer: 'EcoGarden',
            applicationRate: 10,
            description: 'Для всех видов цветов',
            comment: 'Не смешивать с азотом'
        );

        $group = $this->createMock(Group::class);
        $marker = $this->createMock(Marker::class);
        $fertilizerModel = $this->createMock(FertilizerModel::class);

        // 2. Настройка ожиданий
        $this->groupService->expects($this->once())->method('find')->with(1)->willReturn($group);
        $this->markerService->expects($this->once())->method('find')->with(10)->willReturn($marker);

        $this->repository->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(Fertilizer::class));

        $this->repository->expects($this->once())
            ->method('toModel')
            ->willReturn($fertilizerModel);

        // 3. Выполнение
        $result = $this->service->create($model);

        // 4. Проверка
        $this->assertSame($fertilizerModel, $result);
    }

    #[Test]
    public function testUpdateSuccess(): void
    {
        // 1. Подготовка мока сущности
        $fertilizer = $this->createMock(Fertilizer::class);
        $updateModel = new UpdateFertilizerModel(
            groupId: 2,
            markerId: 20,
            title: 'Обновленное удобрение',
            amount: 1000,
            manufacturer: 'BioCorp',
            applicationRate: 5,
            description: 'Новая формула',
            comment: 'Применять утром'
        );

        $group = $this->createMock(Group::class);
        $marker = $this->createMock(Marker::class);
        $fertilizerModel = $this->createMock(FertilizerModel::class);

        // 2. Настройка ожиданий для зависимых сервисов
        $this->groupService->method('find')->with(2)->willReturn($group);
        $this->markerService->method('find')->with(20)->willReturn($marker);

        // 3. Имитируем Fluent Interface: метод moveToGroup должен вернуть сам мок удобрения
        $fertilizer->expects($this->once())
            ->method('moveToGroup')
            ->with($group)
            ->willReturn($fertilizer);

        // 4. Проверяем вызов бизнес-логики в сущности с проверкой типов создаваемых Value Objects
        $fertilizer->expects($this->once())
            ->method('changeFieldsWithMarker')
            ->with(
                $marker,
                $updateModel->title,
                $this->isInstanceOf(PreparationVolume::class),
                $this->isInstanceOf(PreparationDetails::class)
            );

        // 5. Ожидания репозитория
        $this->repository->expects($this->once())->method('update');
        $this->repository->method('toModel')->with($fertilizer)->willReturn($fertilizerModel);

        // 3. Выполнение
        $result = $this->service->update($fertilizer, $updateModel);

        // 4. Проверка
        $this->assertSame($fertilizerModel, $result);
    }

    #[Test]
    public function testRemoveById(): void
    {
        $id = 55;
        $fertilizer = $this->createMock(Fertilizer::class);

        $this->repository->method('find')->with($id)->willReturn($fertilizer);
        $this->repository->expects($this->once())->method('remove')->with($fertilizer);

        $this->service->removeById($id);
    }

    #[Test]
    public function testRemoveByIdNotFound(): void
    {
        $this->repository->method('find')->willReturn(null);
        $this->repository->expects($this->never())->method('remove');

        $this->service->removeById(999);
    }
}
