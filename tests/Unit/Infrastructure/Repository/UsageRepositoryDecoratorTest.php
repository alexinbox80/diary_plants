<?php

namespace Unit\Infrastructure\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Plant;
use App\Domain\Entity\Usage;
use App\Domain\Entity\Watering;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Model\Usage\UsageModel;
use App\Domain\Model\Watering\WateringModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Infrastructure\Repository\UsageRepository;
use App\Domain\Repository\PestRepositoryInterface;
use App\Domain\ValueObject\Usage\AttachableReference;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use App\Domain\Repository\AttachableResolverInterface;
use App\Domain\Repository\WateringRepositoryInterface;
use App\Domain\Repository\StimulantRepositoryInterface;
use App\Domain\Repository\FertilizerRepositoryInterface;
use App\Infrastructure\Repository\GroupRepositoryDecorator;
use App\Infrastructure\Repository\PlantRepositoryDecorator;
use App\Infrastructure\Repository\UsageRepositoryDecorator;

#[CoversClass(UsageRepositoryDecorator::class)]
class UsageRepositoryDecoratorTest extends TestCase
{
    private GroupRepositoryDecorator|MockObject $groupRepo;
    private PlantRepositoryDecorator|MockObject $plantRepo;
    private UsageRepository|MockObject $innerRepo;
    private AttachableResolverInterface|MockObject $resolver;
    private FertilizerRepositoryInterface|MockObject $fertilizerRepo;
    private PestRepositoryInterface|MockObject $pestRepo;
    private StimulantRepositoryInterface|MockObject $stimulantRepo;
    private WateringRepositoryInterface|MockObject $wateringRepo;
    private UsageRepositoryDecorator $decorator;

    protected function setUp(): void
    {
        $this->groupRepo = $this->createMock(GroupRepositoryDecorator::class);
        $this->plantRepo = $this->createMock(PlantRepositoryDecorator::class);
        $this->innerRepo = $this->createMock(UsageRepository::class);
        $this->resolver = $this->createMock(AttachableResolverInterface::class);
        $this->fertilizerRepo = $this->createMock(FertilizerRepositoryInterface::class);
        $this->pestRepo = $this->createMock(PestRepositoryInterface::class);
        $this->stimulantRepo = $this->createMock(StimulantRepositoryInterface::class);
        $this->wateringRepo = $this->createMock(WateringRepositoryInterface::class);

        $this->decorator = new UsageRepositoryDecorator(
            $this->groupRepo,
            $this->plantRepo,
            $this->innerRepo,
            $this->resolver,
            $this->fertilizerRepo,
            $this->pestRepo,
            $this->stimulantRepo,
            $this->wateringRepo
        );
    }

    #[Test]
    public function testFindModelReturnsCorrectModel(): void
    {
        $id = 1;
        $usage = $this->createUsageMock($id);

        $this->innerRepo->method('find')->with($id)->willReturn($usage);

        $this->groupRepo->method('toModel')->willReturn($this->createMock(GroupModel::class));
        $this->plantRepo->method('toModel')->willReturn($this->createMock(PlantModel::class));

        $result = $this->decorator->findModel($id);

        $this->assertInstanceOf(UsageModel::class, $result);
        $this->assertSame($id, $result->getId());
    }

    #[Test]
    public function testToModelWithRelationsResolvesWatering(): void
    {
        $usage = $this->createUsageMock(10);

        $this->groupRepo->method('toModel')->willReturn($this->createMock(GroupModel::class));
        $this->plantRepo->method('toModel')->willReturn($this->createMock(PlantModel::class));

        // 1. Создаем реальный объект в обход конструктора через Reflection
        // Это гарантирует, что get_class($wateringEntity) вернет точную строку "Watering"
        $reflection = new \ReflectionClass(Watering::class);
        $wateringEntity = $reflection->newInstanceWithoutConstructor();

        // 2. Имитируем, что резолвер нашел этот объект
        $this->resolver->expects($this->once())
            ->method('resolve')
            ->willReturn($wateringEntity);

        // 3. Настраиваем репозиторий полива.
        // Поскольку мы передаем реальный объект, мок репозитория должен его принять.
        $this->wateringRepo->method('toModel')
            ->with($wateringEntity, true)
            ->willReturn($this->createMock(
                WateringModel::class));

        // Выполнение
        $result = $this->decorator->toModel($usage, true);

        $this->assertInstanceOf(UsageModel::class, $result);
    }

    private function createUsageMock(int $id): MockObject
    {
        // Используем стаб для корректного перехвата методов Трейтов и типизированных свойств
        $usage = $this->getMockBuilder(UsageStub::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getId', 'getGroup', 'getPlant', 'getUseDate',
                'getTarget', 'getCreatedAt', 'getUpdatedAt', 'getComment',
                'getLoadedAttachment'
            ])
            ->getMock();

        $target = $this->createMock(AttachableReference::class);
        $target->method('getUsableType')->willReturn(AttachableType::WATERING);
        $target->method('getUsableId')->willReturn(50);

        $usage->method('getId')->willReturn($id);
        $usage->method('getUseDate')->willReturn(new DateTimeImmutable());
        $usage->method('getTarget')->willReturn($target);
        $usage->method('getComment')->willReturn('Care check');
        $usage->method('getGroup')->willReturn($this->createMock(Group::class));
        $usage->method('getPlant')->willReturn($this->createMock(Plant::class));
        $usage->method('getLoadedAttachment')->willReturn(null);

        $now = new DateTimeImmutable();
        $usage->method('getCreatedAt')->willReturn($now);
        $usage->method('getUpdatedAt')->willReturn($now);

        return $usage;
    }
}

/**
 * Стаб для Usage для обхода инициализации типизированных свойств в PHPUnit 12.
 */
abstract class UsageStub extends Usage
{
    public function getId(): int { return 0; }

    public function getUseDate(): DateTimeImmutable { return new DateTimeImmutable(); }

    // ИСПРАВЛЕНИЕ: Указываем полный путь к классу именно из пространства Usage
    public function getTarget(): AttachableReference
    {
        return Assert::createMock(AttachableReference::class);
    }

    public function getCreatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
    public function getUpdatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
}
