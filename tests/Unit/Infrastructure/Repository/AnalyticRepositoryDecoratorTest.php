<?php

namespace Unit\Infrastructure\Repository;

use App\Domain\Entity\Group;
use App\Domain\Entity\Plant;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Analytic;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use App\Domain\Model\Analytic\AnalyticModel;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\ValueObject\Analytic\IntervalMetrics;
use App\Infrastructure\Repository\AnalyticRepository;
use App\Infrastructure\Repository\AnalyticRepositoryDecorator;

#[CoversClass(AnalyticRepositoryDecorator::class)]
class AnalyticRepositoryDecoratorTest extends TestCase
{
    private AnalyticRepository|MockObject $innerRepository;
    private AnalyticRepositoryDecorator $decorator;

    protected function setUp(): void
    {
        $this->innerRepository = $this->createMock(AnalyticRepository::class);
        $this->decorator = new AnalyticRepositoryDecorator($this->innerRepository);
    }

    #[Test]
    public function testFindDelegatesToInnerRepository(): void
    {
        $analytic = $this->createMock(Analytic::class);
        $this->innerRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($analytic);

        $result = $this->decorator->find(1);
        $this->assertSame($analytic, $result);
    }

    #[Test]
    public function testFindAllReturnsModels(): void
    {
        $entity1 = $this->createMock(Analytic::class);
        $entity2 = $this->createMock(Analytic::class);

        // Настраиваем сущности так, чтобы AnalyticModel::fromEntity не падал
        // (Мокаем методы, которые вызываются внутри AnalyticModel::fromEntity)
        $this->setupAnalyticMock($entity1);
        $this->setupAnalyticMock($entity2);

        $this->innerRepository->method('findAll')->willReturn([$entity1, $entity2]);

        $result = $this->decorator->findAll();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(AnalyticModel::class, $result[0]);
        $this->assertInstanceOf(AnalyticModel::class, $result[1]);
    }

    #[Test]
    public function testGetAnalyticsPaginatedReturnsMappedResult(): void
    {
        $entity = $this->createMock(Analytic::class);
        $this->setupAnalyticMock($entity);

        $paginationData = [
            'items' => [$entity],
            'pagination' => ['total' => 1, 'page' => 1]
        ];

        $this->innerRepository->method('getAnalyticsPaginated')
            ->with(1, 10)
            ->willReturn($paginationData);

        $result = $this->decorator->getAnalyticsPaginated(1, 10);

        $this->assertArrayHasKey('analyticsModel', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertInstanceOf(AnalyticModel::class, $result['analyticsModel'][0]);
        $this->assertSame($paginationData['pagination'], $result['pagination']);
    }

    #[Test]
    public function testGetAnalyticsPaginatedThrowsExceptionOnInvalidItems(): void
    {
        $this->innerRepository->method('getAnalyticsPaginated')
            ->willReturn(['items' => 'not_an_array']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected array for Analytics');

        $this->decorator->getAnalyticsPaginated(1, 10);
    }

    #[Test]
    public function testCreateDelegatesAndReturnsId(): void
    {
        $entity = $this->createMock(Analytic::class);
        $this->innerRepository->expects($this->once())
            ->method('create')
            ->with($entity)
            ->willReturn(123);

        $result = $this->decorator->create($entity);
        $this->assertSame(123, $result);
    }

    /**
     * Вспомогательный метод для настройки мока Analytic,
     * чтобы статический метод AnalyticModel::fromEntity мог отработать.
     */
    private function setupAnalyticMock(MockObject $analytic): void
    {
        $plant = $this->createMock(Plant::class);
        $group = $this->createMock(Group::class);
        $metrics = $this->createMock(IntervalMetrics::class);

        $analytic->method('getId')->willReturn(rand(1, 100));
        $analytic->method('getPlant')->willReturn($plant);
        $analytic->method('getGroup')->willReturn($group);
        $analytic->method('getWateringMetrics')->willReturn($metrics);
        $analytic->method('getCreatedAt')->willReturn(new \DateTimeImmutable());
        $analytic->method('getUpdatedAt')->willReturn(new \DateTimeImmutable());
    }
}
