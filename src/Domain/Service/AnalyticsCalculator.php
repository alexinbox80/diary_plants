<?php

namespace App\Domain\Service;

use DateTimeImmutable;
use DateTimeInterface;
use DateMalformedStringException;
use App\Domain\ValueObject\Analytic\IntervalMetrics;

class AnalyticsCalculator
{
    /**
     * Расчет нового накопительного среднего
     *
     * @param float $currentAverage
     * @param int $newCount
     * @param float $newValue
     * @return float
     */
    public function calculateNewAverage(float $currentAverage, int $newCount, float $newValue): float
    {
        if ($newCount <= 0) {
            return $newValue;
        }
                // Mn+1 = Mn + (Value - Mn) / (N + 1)
        return $currentAverage + (($newValue - $currentAverage) / $newCount);
    }

    /**
     * Прогноз следующей даты
     *
     * @param DateTimeInterface $lastDate
     * @param float $averageInterval
     * @return DateTimeImmutable
     * @throws DateMalformedStringException
     */
    public function predictNextDate(DateTimeInterface $lastDate, float $averageInterval): DateTimeImmutable
    {
        $days = (int) round($averageInterval);
        return DateTimeImmutable::createFromInterface($lastDate)->modify("+{$days} days");
    }

    /**
     * Рассчитывает метрики на основе полной истории дат
     *
     * @param DateTimeInterface[] $dates
     * @return IntervalMetrics
     */
    public function calculateFullMetrics(array $dates): IntervalMetrics
    {
        $count = 0;
        $average = 0.0;

        if (count($dates) >= 2) {
            // Важно: даты должны быть отсортированы от старых к новым
            usort($dates, fn($a, $b) => $a <=> $b);

            $lastDate = null;
            foreach ($dates as $currentDate) {
                if ($lastDate !== null) {
                    $diff = $currentDate->diff($lastDate);
                    $daysPassed = (float) $diff->days;
                    $count++;
                    $average = $this->calculateNewAverage($average, $count, $daysPassed);
                }
                $lastDate = $currentDate;
            }
        }

        return new IntervalMetrics($count, $average);
    }
}
