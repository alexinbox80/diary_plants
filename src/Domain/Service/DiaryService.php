<?php

namespace App\Domain\Service;

use DateTimeZone;
use DateTimeImmutable;

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

            $acc[$type][] = [
                'id' => $marker->getId(),
                'letter' => $marker->getMarker()->getLetter(),
                'description' => $marker->getDescription(),
                'color' => $marker->getMarker()->getColor(),
                'type' => $type,
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

        if ($year === null && $month === null)
            $date = new DateTimeImmutable('first day of this month');
        else
            $date = new DateTimeImmutable($year . '-' . $month . '-1');

        $timezone = new DateTimeZone('Europe/Moscow');
        $currentDay = new DateTimeImmutable()->setTimezone($timezone);

        $daysInMonth = $date->format('t');

        $days = [];
        foreach (range(1, $daysInMonth) as $day) {
            $date = DateTimeImmutable::createFromFormat('j', $day);
            $index = $date->format('N');
            $days[] = ['num_day' => $day, 'name_day' => $daysRu[$index]];
        }

        return [
            'tableHeader' => $days,
            'currentDay' => $currentDay->format('j')
        ];
    }

    public function getDiaryBody(): array
    {
        $plants = $this->plantService->getPlantsForDiary(2);

        return [
            'tableBody' => $plants
        ];
    }

    public function getDiaryTitle(?int $year = null, ?int $month = null): array
    {
        $timezone = new DateTimeZone('Europe/Moscow');
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
