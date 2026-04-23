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
            amount: 100,
            applicationRate: 2,
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
            amount: 250,
            applicationRate: 5,
            manufacturer: 'GardenSafe',
            description: 'Улучшенная формула',
            comment: 'Хранить в тени'
        );

        $this->groupService->method('find')->willReturn($this->createMock(Group::class));
        $this->markerService->method('find')->willReturn($this->createMock(Marker::class));

        // Проверяем вызов бизнес-логики в сущности
        $pestEntity->expects($this->once())->method('changeFieldsWithMarker');
        $this->repository->expects($this->once())->method('update');

        $this->service->update($pestEntity, $updateModel);
    }

    #[Test]
    public function testRemoveById(): void
    {
        $id = 77;
        $pest = $this->createMock(Pest::class);

        $this->repository->method('find')->with($id)->willReturn($pest);
        $this->repository->expects($this->once())->method('remove')->with($pest);

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
