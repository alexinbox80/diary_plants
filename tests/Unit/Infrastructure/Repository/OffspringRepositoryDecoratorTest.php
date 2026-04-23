<?php

namespace Unit\Infrastructure\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Plant;
use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Offspring;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Model\Group\GroupModel;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Model\Offspring\OffspringModel;
use App\Domain\ValueObject\Offspring\Phenology;
use App\Domain\ValueObject\Offspring\FruitMetrics;
use App\Domain\Repository\GroupRepositoryInterface;
use App\Domain\Repository\PlantRepositoryInterface;
use App\Infrastructure\Repository\OffspringRepository;
use App\Infrastructure\Repository\OffspringRepositoryDecorator;

#[CoversClass(OffspringRepositoryDecorator::class)]
class OffspringRepositoryDecoratorTest extends TestCase
{
    private OffspringRepository|MockObject $innerRepository;
    private PlantRepositoryInterface|MockObject $plantRepo;
    private GroupRepositoryInterface|MockObject $groupRepo;
    private OffspringRepositoryDecorator $decorator;

    protected function setUp(): void
    {
        $this->innerRepository = $this->createMock(OffspringRepository::class);
        $this->plantRepo = $this->createMock(PlantRepositoryInterface::class);
        $this->groupRepo = $this->createMock(GroupRepositoryInterface::class);

        $this->decorator = new OffspringRepositoryDecorator(
            $this->innerRepository,
            $this->plantRepo,
            $this->groupRepo
        );
    }

    #[Test]
    public function testFindModelReturnsCorrectModel(): void
    {
        $id = 1;
        $offspring = $this->createOffspringMock($id);

        $this->innerRepository->method('find')->with($id)->willReturn($offspring);

        $result = $this->decorator->findModel($id);

        $this->assertInstanceOf(OffspringModel::class, $result);
        $this->assertSame($id, $result->getId());
    }

    #[Test]
    public function testFindAllReturnsModelsWithRelations(): void
    {
        $offspring = $this->createOffspringMock(10);
        $this->innerRepository->method('findAll')->willReturn([$offspring]);

        // Настраиваем связанные модели (addRelations = true)
        $this->plantRepo->method('toModel')->willReturn($this->createMock(PlantModel::class));
        $this->groupRepo->method('toModel')->willReturn($this->createMock(GroupModel::class));

        $results = $this->decorator->findAll();

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertInstanceOf(OffspringModel::class, $results[0]);
    }

    #[Test]
    public function testGetOffspringsPaginatedThrowsExceptionOnInvalidData(): void
    {
        $this->innerRepository->method('getOffspringsPaginatedWithAttachments')
            ->willReturn(['items' => 'not_array']);

        $this->expectException(\InvalidArgumentException::class);
        $this->decorator->getOffspringsPaginated(1, 10);
    }

    private function createOffspringMock(int $id): MockObject
    {
        // Используем стаб для PHPUnit 12
        $offspring = $this->getMockBuilder(OffspringStub::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getId', 'getGroup', 'getPlant', 'getPhenology',
                'getFruitMetrics', 'getComment', 'getLoadedAttachments',
                'getCreatedAt', 'getUpdatedAt'
            ])
            ->getMock();

        $phenology = $this->createMock(Phenology::class);
        $metrics = $this->createMock(FruitMetrics::class);

        // Настройка возвращаемых значений для предотвращения TypeError в модели
        $offspring->method('getId')->willReturn($id);
        $offspring->method('getPhenology')->willReturn($phenology);
        $offspring->method('getFruitMetrics')->willReturn($metrics);
        $offspring->method('getComment')->willReturn('Great harvest');
        $offspring->method('getGroup')->willReturn($this->createMock(Group::class));
        $offspring->method('getPlant')->willReturn($this->createMock(Plant::class));
        $offspring->method('getLoadedAttachments')->willReturn([]);

        $now = new DateTimeImmutable();
        $offspring->method('getCreatedAt')->willReturn($now);
        $offspring->method('getUpdatedAt')->willReturn($now);

        return $offspring;
    }
}

/**
 * Стаб для обхода неинициализированных свойств (phenology, metrics) и трейтов.
 */
abstract class OffspringStub extends Offspring
{
    public function getId(): int { return 0; }
    public function getPhenology(): Phenology {
        return Assert::createMock(Phenology::class);
    }
    public function getFruitMetrics(): FruitMetrics {
        return Assert::createMock(FruitMetrics::class);
    }
    public function getCreatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
    public function getUpdatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
    public function getComment(): ?string { return null; }
    public function getLoadedAttachments(): array { return []; }
}
