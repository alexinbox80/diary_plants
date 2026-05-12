<?php

namespace Unit\Domain\Service;

use App\Domain\Entity\Pest;
use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use PHPUnit\Framework\TestCase;
use App\Domain\Service\PestService;
use App\Domain\Service\GroupService;
use App\Domain\Service\ModelFactory;
use App\Domain\Model\Pest\PestModel;
use App\Domain\Service\MarkerService;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Model\Pest\CreatePestModel;
use App\Domain\Model\Pest\UpdatePestModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Repository\PestRepositoryInterface;
use App\Domain\ValueObject\Preparation\PreparationVolume;
use App\Domain\ValueObject\Preparation\PreparationDetails;

#[CoversClass(PestService::class)]
class PestServiceTest extends TestCase
{
    private GroupService|MockObject $groupService;
    private MarkerService|MockObject $markerService;
    private ModelFactory|MockObject $modelFactory;
    private PestRepositoryInterface|MockObject $repository;
    private PestService $service;

    protected function setUp(): void
    {
        $this->groupService = $this->createMock(GroupService::class);
        $this->markerService = $this->createMock(MarkerService::class);
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->repository = $this->createMock(PestRepositoryInterface::class);

        $this->service = new PestService(
            $this->groupService,
            $this->markerService,
            $this->modelFactory,
            $this->repository
        );
    }

    #[Test]
    public function testCreateSuccess(): void
    {
        $model = new CreatePestModel(
            groupId: 1,
            markerId: 5,
            title: 'Анти-Тля',
            amount: 100.0,
            applicationRate: '2 ml/l',
            manufacturer: 'BioDefense',
            description: 'Концентрат',
            comment: 'Опасно для пчел'
        );

        $group = $this->createMock(Group::class);
        $marker = $this->createMock(Marker::class);
        $pestModel = $this->createMock(PestModel::class);

        $this->groupService->method('find')->with(1)->willReturn($group);
        $this->markerService->method('find')->with(5)->willReturn($marker);

        $this->repository->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(Pest::class));

        $this->repository->method('toModel')->willReturn($pestModel);

        $result = $this->service->create($model);

        $this->assertSame($pestModel, $result);
    }

    #[Test]
    public function testUpdateSuccess(): void
    {
        $pestEntity = $this->createMock(Pest::class);
        $updateModel = new UpdatePestModel(
            groupId: 2,
            markerId: 8,
            title: 'Новый Состав',
            amount: 250.0,
            applicationRate: '5 ml/l',
            manufacturer: 'GardenSafe',
            description: 'Улучшенная формула',
            comment: 'Хранить в тени'
        );

        $group = $this->createMock(Group::class);
        $marker = $this->createMock(Marker::class);

        $this->groupService->method('find')->with(2)->willReturn($group);
        $this->markerService->method('find')->with(8)->willReturn($marker);

        // Имитируем цепочку вызовов: moveToGroup должен вернуть сам объект $pestEntity
        $pestEntity->expects($this->once())
            ->method('moveToGroup')
            ->with($group)
            ->willReturn($pestEntity);

        // Проверяем вызов бизнес-логики в сущности
        $pestEntity->expects($this->once())
            ->method('changeFieldsWithMarker')
            ->with(
                $marker,
                $updateModel->title,
                $this->isInstanceOf(PreparationVolume::class),
                $this->isInstanceOf(PreparationDetails::class)
            );

        $this->repository->expects($this->once())->method('update');
        $this->repository->method('toModel')->willReturn($this->createMock(PestModel::class));

        $this->service->update($pestEntity, $updateModel);
    }

    #[Test]
    public function testRemoveById(): void
    {
        $id = 77;
        $pest = $this->createMock(Pest::class);

        // Важно: репозиторий должен возвращать Pest, а не Marker
        $this->repository->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($pest);

        $this->repository->expects($this->once())
            ->method('remove')
            ->with($pest);

        $this->service->removeById($id);
    }

    #[Test]
    public function testRemoveByIdNotFound(): void
    {
        $this->repository->method('find')->willReturn(null);
        $this->repository->expects($this->never())->method('remove');

        $this->service->removeById(404);
    }
}
