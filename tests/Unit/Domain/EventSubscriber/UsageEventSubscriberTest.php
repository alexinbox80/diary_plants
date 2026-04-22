<?php

namespace Unit\Domain\EventSubscriber;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Analytic;
use App\Domain\Service\PlantService;
use App\Domain\Service\UsageService;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Service\AnalyticService;
use App\Domain\Event\UsageIsCreatedEvent;
use App\Domain\Service\AnalyticsCalculator;
use App\Domain\Event\UsagesBulkDeletedEvent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\EventSubscriber\UsageEventSubscriber;
use App\Domain\ValueObject\Analytic\IntervalMetrics;
use App\Domain\ValueObject\Enum\Usage\AttachableType;

#[CoversClass(UsageEventSubscriber::class)]
class UsageEventSubscriberTest extends TestCase
{
    private AnalyticService|MockObject $analyticService;
    private AnalyticsCalculator|MockObject $calculator;
    private UsageService|MockObject $usageService;
    private PlantService|MockObject $plantService;
    private UsageEventSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->analyticService = $this->createMock(AnalyticService::class);
        $this->calculator = $this->createMock(AnalyticsCalculator::class);
        $this->usageService = $this->createMock(UsageService::class);
        $this->plantService = $this->createMock(PlantService::class);

        $this->subscriber = new UsageEventSubscriber(
            $this->analyticService,
            $this->calculator,
            $this->usageService,
            $this->plantService
        );
    }

    #[Test]
    public function testOnUsageIsCreatedIncrementsStats(): void
    {
        $event = new UsageIsCreatedEvent(
            id: 100,
            groupId: 2,
            useDate: new DateTimeImmutable('2024-01-10'),
            plantId: 1,
            usableId: 10,
            usableType: AttachableType::WATERING->value
        );

        $analytic = $this->createMock(Analytic::class);
        $metrics = new IntervalMetrics(1, 5.0); // текущие: 1 полив, ср. 5 дней

        $this->analyticService->method('getEntityByPlantId')->with(1)->willReturn($analytic);
        $analytic->method('getWateringMetrics')->willReturn($metrics);

        // Находим предыдущую дату (например, 5 января)
        $this->usageService->method('findLatestDateBefore')->willReturn(new DateTimeImmutable('2024-01-05'));

        // Ожидаем расчет нового среднего (разница 5 дней)
        $this->calculator->expects($this->once())
            ->method('calculateNewAverage')
            ->with(5.0, 2, 5.0)
            ->willReturn(5.0);

        // Проверяем, что сервис аналитики вызван с новыми данными
        $this->analyticService->expects($this->once())
            ->method('updateWateringMetrics')
            ->with(1, $this->callback(fn(IntervalMetrics $m) => $m->getCount() === 2));

        $this->subscriber->onUsageIsCreated($event);
    }

    #[Test]
    public function testOnUsagesBulkDeletedTriggersRecalculation(): void
    {
        $event = new UsagesBulkDeletedEvent([1, 2], AttachableType::WATERING->value);

        // Создаем мок правильного класса PlantModel
        $plantModel = $this->createMock(PlantModel::class);

        // Настраиваем, чтобы findModel возвращал этот мок
        $this->plantService->expects($this->exactly(2))
            ->method('findModel')
            ->willReturn($plantModel);

        // Проверяем, что для каждого растения вызвалось обновление метрик
        $this->analyticService->expects($this->exactly(2))
            ->method('updateWateringMetrics');

        $this->subscriber->onUsagesBulkDeleted($event);
    }

    #[Test]
    public function testIgnoresNonWateringEvents(): void
    {
        $event = new UsageIsCreatedEvent(
            id: 50,
            groupId: 2,
            useDate: new DateTimeImmutable('2025-11-15'),
            plantId: 5,
            usableId: 15,
            usableType: AttachableType::FERTILIZER->value
        );

        $this->analyticService->expects($this->never())->method('getEntityByPlantId');

        $this->subscriber->onUsageIsCreated($event);
    }
}
