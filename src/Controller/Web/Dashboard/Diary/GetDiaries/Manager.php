<?php

namespace App\Controller\Web\Dashboard\Diary\GetDiaries;

use App\Domain\Service\DiaryService;

class Manager
{
    public function __construct(
        private readonly DiaryService $diaryService
    ) {
    }

    /**
     * @return array
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getDiaries(?int $year = null, ?int $month = null): array
    {
        $diaryType = $this->diaryService->getDiaryType();
        $diaryTitle = $this->diaryService->getDiaryTitle($year, $month);
        $tableHeader = $this->diaryService->getDiaryHeader($year, $month);
        $tableBody = $this->diaryService->getDiaryBody();

        return [
            'diaryType' => $diaryType,
            'diaryTitle' => $diaryTitle,
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}
