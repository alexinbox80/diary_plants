<?php

namespace Unit\Domain\Service;

use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Plant;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Repotting;
use App\Domain\Service\GroupService;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\PlantService;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Service\RepottingService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Model\Repotting\RepottingModel;
use App\Domain\Model\Repotting\CreateRepottingModel;
use App\Domain\Model\Repotting\UpdateRepottingModel;
use App\Domain\ValueObject\Enum\Repotting\PotMaterial;
use App\Domain\Repository\RepottingRepositoryInterface;
use App\Domain\ValueObject\Enum\Repotting\RepottingType;

#[CoversClass(RepottingService::class)]
class RepottingServiceTest extends TestCase
{
    private GroupService|MockObject $groupService;
    private PlantService|MockObject $plantService;
    private RepottingRepositoryInterface|MockObject $repository;
    private ModelFactory|MockObject $modelFactory;
    private RepottingService $service;

    protected function setUp(): void
    {
        $this->groupService = $this->createMock(GroupService::class);
        $this->plantService = $this->createMock(PlantService::class);
        $this->repository = $this->createMock(RepottingRepositoryInterface::class);
        $this->modelFactory = $this->createMock(ModelFactory::class);

        $this->service = new RepottingService(
            $this->groupService,
            $this->plantService,
            $this->repository,
            $this->modelFactory
        );
    }

    #[Test]
    public function testCreateSuccess(): void
    {
        // Используем реальный DTO, чтобы не было проблем с readonly/инициализацией
        $model = new CreateRepottingModel(
            groupId: 2,
            plantId: 10,
            repottedAt: new DateTimeImmutable('2024-05-01'),
            type: RepottingType::REPOT_FULL->value,
            potMaterial: PotMaterial::PLASTIC->value,
            potSize: '12cm',
            comment: 'Fresh soil'
        );

        $this->groupService->method('find')->with(2)->willReturn($this->createMock(Group::class));
        $this->plantService->method('find')->with(10)->willReturn($this->createMock(Plant::class));

        $repottingModel = $this->createMock(RepottingModel::class);

        $this->repository->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(Repotting::class));

        $this->repository->method('toModel')->willReturn($repottingModel);

        $result = $this->service->create($model);

        $this->assertSame($repottingModel, $result);
    }

    #[Test]
    public function testUpdateSuccess(): void
    {
        $repottingEntity = $this->createMock(Repotting::class);
        $model = new UpdateRepottingModel(
            groupId: 2,
            plantId: 10,
            repottedAt: new DateTimeImmutable('2024-06-01'),
            type: RepottingType::ROOT_PRUNING->value,
            potMaterial: PotMaterial::CERAMIC->value,
            potSize: '15cm',
            comment: 'New comment'
        );

        $this->groupService->method('find')->willReturn($this->createMock(Group::class));
        $this->plantService->method('find')->willReturn($this->createMock(Plant::class));

        // Проверяем вызовы в сущности
        $repottingEntity->expects($this->once())->method('changeFields');
        $repottingEntity->expects($this->once())->method('setComment')->with('New comment');

        $this->repository->expects($this->once())->method('update');
        $this->repository->method('toModel')->willReturn($this->createMock(RepottingModel::class));

        $this->service->update($repottingEntity, $model);
    }

    #[Test]
    public function testRemoveById(): void
    {
        $entity = $this->createMock(Repotting::class);
        $this->repository->method('find')->with(1)->willReturn($entity);
        $this->repository->expects($this->once())->method('remove')->with($entity);

        $this->service->removeById(1);
    }

    #[Test]
    public function testRemoveByIdNotFound(): void
    {
        $this->repository->method('find')->willReturn(null);
        $this->repository->expects($this->never())->method('remove');

        $this->service->removeById(999);
    }
}
