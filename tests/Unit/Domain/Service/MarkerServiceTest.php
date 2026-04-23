<?php

namespace Unit\Domain\Service;

use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use PHPUnit\Framework\TestCase;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\GroupService;
use App\Domain\Service\MarkerService;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Model\Marker\MarkerModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Model\Marker\CreateMarkerModel;
use App\Domain\Model\Marker\UpdateMarkerModel;
use App\Domain\Repository\MarkerRepositoryInterface;
use App\Domain\ValueObject\Enum\Usage\AttachableType;

#[CoversClass(MarkerService::class)]
class MarkerServiceTest extends TestCase
{
    private GroupService|MockObject $groupService;
    private MarkerRepositoryInterface|MockObject $repository;
    private ModelFactory|MockObject $modelFactory;
    private MarkerService $service;

    protected function setUp(): void
    {
        $this->groupService = $this->createMock(GroupService::class);
        $this->repository = $this->createMock(MarkerRepositoryInterface::class);
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->service = new MarkerService($this->groupService, $this->repository, $this->modelFactory);
    }

    #[Test]
    public function testGetChoicesForChoiceTypeFormatsLabels(): void
    {
        $marker1 = $this->createMock(MarkerModel::class);
        $marker1->method('getId')->willReturn(1);
        $marker1->method('getLetter')->willReturn('W');
        $marker1->method('getDescription')->willReturn('Watering');

        $this->repository->expects($this->once())
            ->method('getMarkersForForm')
            ->willReturn([$marker1]);

        $result = $this->service->getChoicesForChoiceType();

        // Проверяем формат "Буква (Описание) => ID"
        $this->assertSame(['W (Watering)' => 1], $result);
    }

    #[Test]
    public function testCreateSuccess(): void
    {
        $model = new CreateMarkerModel(
            groupId: 1,
            letter: 'F',
            color: '#00FF00',
            type: AttachableType::WATERING->value,
            description: 'Fertilizing',
            colorDescription: 'Green'
        );

        $group = $this->createMock(Group::class);
        $markerModel = $this->createMock(MarkerModel::class);

        $this->groupService->method('find')->with(1)->willReturn($group);

        $this->repository->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(Marker::class));

        $this->repository->expects($this->once())
            ->method('toModel')
            ->willReturn($markerModel);

        $result = $this->service->create($model);

        $this->assertSame($markerModel, $result);
    }

    #[Test]
    public function testUpdateSuccess(): void
    {
        $markerEntity = $this->createMock(Marker::class);
        $updateModel = new UpdateMarkerModel(
            groupId: 2,
            letter: 'S',
            color: '#FF0000',
            type: AttachableType::WATERING->value,
            description: 'Spray',
            colorDescription: 'Red'
        );

        $group = $this->createMock(Group::class);
        $this->groupService->method('find')->willReturn($group);

        $markerEntity->expects($this->once())->method('changeFields');
        $this->repository->expects($this->once())->method('update');

        $this->service->update($markerEntity, $updateModel);
    }

    #[Test]
    public function testRemoveById(): void
    {
        $marker = $this->createMock(Marker::class);
        $this->repository->method('find')->with(1)->willReturn($marker);
        $this->repository->expects($this->once())->method('remove')->with($marker);

        $this->service->removeById(1);
    }
}
