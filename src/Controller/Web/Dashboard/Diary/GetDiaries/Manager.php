<?php

namespace App\Controller\Web\Dashboard\Diary\GetDiaries;

use App\Domain\Service\DiaryService;
use App\Domain\ValueObject\Enum\Timezone;
use App\Application\Security\AccessContext;

class Manager
{
    public function __construct(
        private readonly DiaryService $diaryService,
        private readonly AccessContext $accessContext
    ) {
    }

    /**
     * @param int|null $year
     * @param int|null $month
     * @return array
     */
    public function getDiaries(?int $year = null, ?int $month = null): array
    {
        $groupId = $this->accessContext->getTargetGroupId();
        $timezone = $this->accessContext->getTimezone();

        $diaryType = $this->diaryService->getDiaryType();
        $diaryTitle = $this->diaryService->getDiaryTitle($year, $month, Timezone::tryFrom($timezone));
        $tableHeader = $this->diaryService->getDiaryHeader($year, $month);
        $tableBody = $this->diaryService->getDiaryBody($groupId);

        return [
            'diaryType' => $diaryType,
            'diaryTitle' => $diaryTitle,
            'tableHeader' => $tableHeader,
            'tableBody' => $tableBody
        ];
    }
}
