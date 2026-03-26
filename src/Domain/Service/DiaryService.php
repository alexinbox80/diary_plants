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

        return [
            'waterings'=> $this->markerService->getMarkersForDairy($groupId, 'watering'),
            'pests'=> $this->markerService->getMarkersForDairy($groupId, 'pest'),
            'stimulants'=> $this->markerService->getMarkersForDairy($groupId, 'stimulant'),
            'fertilizers'=> $this->markerService->getMarkersForDairy($groupId, 'fertilizer'),
        ];
    }

    public function getDiaryHeader(): array
    {
        $daysRu = [1 => 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];

        $date = new DateTimeImmutable('first day of this month');

        $daysInMonth = $date->format('t');
        $firstDayOfWeek = $date->format('N');

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
