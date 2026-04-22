<?php

namespace App\Domain\EventSubscriber;

use App\Domain\Service\PlantService;
use App\Domain\Service\UsageService;
use App\Domain\Model\Usage\UsageModel;
use App\Domain\Service\AnalyticService;
use App\Domain\Event\UsageIsCreatedEvent;
use App\Domain\Event\UsageIsDeletedEvent;
use App\Domain\Service\AnalyticsCalculator;
use App\Domain\Event\UsagesBulkDeletedEvent;
use App\Domain\ValueObject\Analytic\IntervalMetrics;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UsageEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly AnalyticService $analyticService,
        private readonly AnalyticsCalculator $calculator,
        private readonly UsageService $usageService,
        private readonly PlantService $plantService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UsageIsCreatedEvent::class => 'onUsageIsCreated',
            UsageIsDeletedEvent::class => 'onUsageIsDeleted',
            UsagesBulkDeletedEvent::class => 'onUsagesBulkDeleted',
        ];
    }

    public function onUsageIsCreated(UsageIsCreatedEvent $event): void
    {
        // Проверяем, что это полив
        if (!AttachableType::isWatering($event->usableType)) {
            return;
        }

        // 1. Получаем текущую аналитику для растения
        $analytic = $this->analyticService->getEntityByPlantId($event->plantId);

        if (!$analytic) {
            // Если аналитики еще нет, возможно, стоит её создать или пропустить
            return;
        }

        $metrics = $analytic->getWateringMetrics();
        $currentAverage = $metrics->getAverageDays();
        $currentCount = $metrics->getCount();

        // 2. Рассчитываем интервал с момента последнего полива
        // В событии должна быть дата нового полива
        $newUsageDate = $event->useDate;
        $lastUsageDate = $this->usageService->findLatestDateBefore(
            $event->groupId,
            $event->usableId,
            $event->usableType,
            $event->useDate,
        );

        if ($lastUsageDate === null) {
            // Это первый зафиксированный полив, среднее пока 0, но дату сохранить нужно
            $this->updateMetrics($event->plantId, 0, 0.0);
            return;
        }

        $diff = $newUsageDate->diff($lastUsageDate);
        $daysPassed = (float) $diff->days;

        // 3. Инкрементальный расчет среднего:Mn+1 = Mn + (Value - Mn) / (N + 1)
        $newCount = $currentCount + 1;
        $newAverage = $this->calculator->calculateNewAverage(
            $currentAverage,
            $newCount,
            $daysPassed
        );

        // 4. Сохраняем обновленные метрики
        $this->updateMetrics($event->plantId, $newCount, $newAverage);
    }

    public function onUsageIsDeleted(UsageIsDeletedEvent $event): void
    {
        if (!AttachableType::isWatering($event->usableType)) {
            return;
        }

        // При удалении лучше всего выполнить полный пересчет,
        // так как удаление ломает цепочку интервалов между соседями.
        $this->recalculateFullStats($event->plantId);
    }

    public function onUsagesBulkDeleted(UsagesBulkDeletedEvent $event): void
    {
        if (!AttachableType::isWatering($event->usableType)) {
            return;
        }

        foreach ($event->plantIds as $plantId) {
            // Переиспользуем метод полного пересчета
            $this->recalculateFullStats($plantId);
        }
    }

    private function recalculateFullStats(int $plantId): void
    {
        $plant = $this->plantService->findModel($plantId);
        if (!$plant) return;

        $waterings = $this->usageService->findBy($plant, AttachableType::WATERING->value);

        // Извлекаем только даты из коллекции Usage
        $dates = array_map(fn(UsageModel $usage) => $usage->getUseDate(), $waterings);

        // Делегируем расчет калькулятору
        $metrics = $this->calculator->calculateFullMetrics($dates);

        // Сохраняем результат
        $this->analyticService->updateWateringMetrics($plantId, $metrics);
    }

    private function updateMetrics(int $plantId, int $count, float $average): void
    {
        $this->analyticService->updateWateringMetrics(
            $plantId,
            new IntervalMetrics($count, $average)
        );
    }
}
