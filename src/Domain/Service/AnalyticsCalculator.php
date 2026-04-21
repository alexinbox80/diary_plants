<?php

namespace App\Domain\Service;

use DateTimeImmutable;
use DateTimeInterface;
use DateMalformedStringException;

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
}
