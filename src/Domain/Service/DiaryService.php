<?php

namespace App\Domain\Service;

use DateTimeImmutable;

class DiaryService
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly MarkerService $markerService
    ) {
    }

    public function getDiaryType(): array
    {
        $groupId = 2;

        $markers = $this->markerService->getMarkersForDairy($groupId);

        $groupedData = [];
        foreach ($markers as $marker) {
            $groupedData[$marker['type']][] = $marker;
        }

        return [
            'waterings'=> $groupedData['watering'] ?? [],
            'pests'=> $groupedData['pest'] ?? [],
            'stimulants'=> $groupedData['stimulant'] ?? [],
            'fertilizers'=> $groupedData['fertilizer'] ?? [],
        ];
    }

    public function getDiaryHeader(): array
    {
        $daysRu = [1 => 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

        $date = new DateTimeImmutable('first day of this month');

        $daysInMonth = $date->format('t');

        $days = [];
        foreach (range(1, $daysInMonth) as $day) {
            $date = DateTimeImmutable::createFromFormat('j', $day);
            $index = $date->format('N');
            $days[] = ['num_day' => $day, 'name_day' => $daysRu[$index]];
        }

        return [
            'tableHeader' => $days,
        ];
    }

    public function getDiaryBody(): array
    {
        $plants = $this->plantService->getPlantsForDiary(2);

        return [
            'tableBody' => $plants
        ];
    }

}
