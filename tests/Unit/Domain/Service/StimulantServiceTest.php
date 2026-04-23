<?php

namespace Unit\Domain\Service;

use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Stimulant;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\GroupService;
use App\Domain\Service\MarkerService;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Service\StimulantService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Model\Stimulant\StimulantModel;
use App\Domain\Model\Stimulant\CreateStimulantModel;
use App\Domain\Model\Stimulant\UpdateStimulantModel;
use App\Domain\Repository\StimulantRepositoryInterface;

#[CoversClass(StimulantService::class)]
class StimulantServiceTest extends TestCase
{
    private GroupService|MockObject $groupService;
    private MarkerService|MockObject $markerService;
    private ModelFactory|MockObject $modelFactory;
    private StimulantRepositoryInterface|MockObject $repository;
    private StimulantService $service;

    protected function setUp(): void
    {
        $this->groupService = $this->createMock(GroupService::class);
        $this->markerService = $this->createMock(MarkerService::class);
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->repository = $this->createMock(StimulantRepositoryInterface::class);

        $this->service = new StimulantService(
            $this->groupService,
            $this->markerService,
            $this->modelFactory,
            $this->repository
        );
    }

    #[Test]
    public function testCreateSuccess(): void
    {
        // Используем реальную модель
        $model = new CreateStimulantModel(
            groupId: 1,
            markerId: 10,
            title: 'Эпин-Экстра',
            amount: 1,
            applicationRate: 0.2,
            manufacturer: 'НЭСТ М',
            description: 'Адаптоген',
            comment: 'Опрыскивание по листу'
        );

        $group = $this->createMock(Group::class);
        $marker = $this->createMock(Marker::class);
        $stimulantModel = $this->createMock(StimulantModel::class);

        $this->groupService->method('find')->with(1)->willReturn($group);
        $this->markerService->method('find')->with(10)->willReturn($marker);

        $this->repository->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(Stimulant::class));

        $this->repository->method('toModel')->willReturn($stimulantModel);

        $result = $this->service->create($model);

        $this->assertSame($stimulantModel, $result);
    }

    #[Test]
    public function testUpdateSuccess(): void
    {
        $stimulantEntity = $this->createMock(Stimulant::class);
        $updateModel = new UpdateStimulantModel(
            groupId: 2,
            markerId: 20,
            title: 'Циркон',
            amount: 5,
            applicationRate: 1,
            manufacturer: 'НЭСТ М',
            description: 'Корнеобразователь',
            comment: 'Полив'
        );

        $this->groupService->method('find')->willReturn($this->createMock(Group::class));
        $this->markerService->method('find')->willReturn($this->createMock(Marker::class));

        // Проверяем вызов бизнес-логики в сущности
        $stimulantEntity->expects($this->once())->method('changeFieldsWithMarker');
        $this->repository->expects($this->once())->method('update');

        $this->service->update($stimulantEntity, $updateModel);
    }

    #[Test]
    public function testRemoveById(): void
    {
        $id = 5;
        $stimulant = $this->createMock(Stimulant::class);

        $this->repository->method('find')->with($id)->willReturn($stimulant);
        $this->repository->expects($this->once())->method('remove')->with($stimulant);

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
