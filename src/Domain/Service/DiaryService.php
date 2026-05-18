<?php

namespace App\Domain\Service;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\ValueObject\Enum\Timezone;

class DiaryService
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly WateringService $wateringService,
        private readonly PestService $pestService,
        private readonly FertilizerService $fertilizerService,
        private readonly StimulantService $stimulantService
    ) {
    }

    public function getDiaryType(): array
    {
        $groupId = 2;

        $waterings = $this->wateringService->getWateringsForDairy($groupId);
        $pests = $this->pestService->getPestsForDairy($groupId);
        $fertilizers = $this->fertilizerService->getFertilizersForDairy($groupId);
        $stimulants = $this->stimulantService->getStimulantsForDairy($groupId);

        $allUsages = array_merge($waterings, $pests, $fertilizers, $stimulants);

        $groupedData = array_reduce($allUsages, function (array $acc, $marker) {
            $type = $marker->getMarker()->getType();
            $typeKey = ($type instanceof \BackedEnum) ? $type->value : (string) $type;

            $acc[$typeKey][] = [
                'id' => $marker->getId(),
                'letter' => $marker->getMarker()->getLetter(),
                'description' => $marker->getDescription(),
                'color' => $marker->getMarker()->getColor(),
                'type' => $typeKey,
            ];

            return $acc;
        }, []);

        return [
            'watering'=> $groupedData['watering'] ?? [],
            'pest'=> $groupedData['pest'] ?? [],
            'stimulant'=> $groupedData['stimulant'] ?? [],
            'fertilizer'=> $groupedData['fertilizer'] ?? [],
        ];
    }

    public function getDiaryHeader(?int $year = null, ?int $month = null): array
    {
        $daysRu = [1 => 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

        if ($year === null || $month === null) {
            $now = new DateTimeImmutable('now');
            $year = $year ?? (int)$now->format('Y');
            $month = $month ?? (int)$now->format('n');
        }

        $date = new DateTimeImmutable($year . '-' . $month . '-1');

        $timezone = new DateTimeZone('Europe/Moscow');
        $currentDay = new DateTimeImmutable()->setTimezone($timezone);

        $daysInMonth = (int) $date->format('t');

        $days = [];
        foreach (range(1, $daysInMonth) as $day) {
            $fullDate = new DateTimeImmutable("$year-$month-$day");
            $index = $fullDate->format('N');
            $days[] = ['num_day' => $day, 'name_day' => $daysRu[$index]];
        }

        return [
            'tableHeader' => $days,
            'currentDay' => $currentDay->format('j')
        ];
    }

    /**
     * @param int|null $groupId
     * @return array
     */
    public function getDiaryBody(?int $groupId = null): array
    {
        $plants = $this->plantService->getPlantsForDiary($groupId);

        return [
            'tableBody' => $plants
        ];
    }

    public function getDiaryTitle(?int $year = null, ?int $month = null, ?Timezone $tz = null): array
    {
        if (is_null($tz)) {
            $timezone = new DateTimeZone('Europe/Moscow');
        } else {
            $timezone = new DateTimeZone($tz->value);
        }

        $date = new DateTimeImmutable()->setTimezone($timezone);

        if ($month === null) {
            $month = $date->format('n');
        }

        if ($year === null) {
            $year = $date->format('Y');
        }

        return [
            'month' => $month,
            'year' => $year,
        ];
    }
}
