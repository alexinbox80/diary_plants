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
    public function getDiaries(): array
    {
        $dairyType = $this->diaryService->getDiaryType();
        $tableHeader = $this->diaryService->getDiaryHeader();
        $tableBody = $this->diaryService->getDiaryBody();

        return [
            'dairyType' => $dairyType,
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}
