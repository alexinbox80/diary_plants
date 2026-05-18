<?php

namespace Unit\Controller\Web\Dashboard\Diary\GetDiaries;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\DiaryService;
use App\Application\Security\AccessContext;
use App\Controller\Web\Dashboard\Diary\GetDiaries\Manager;


class ManagerTest extends TestCase
{
    private DiaryService $diaryService;
    private Manager $manager;

    protected function setUp(): void
    {
        $this->diaryService = $this->createMock(DiaryService::class);
        // 1. Создаем мок для контекста доступа
        $accessContext = $this->createMock(AccessContext::class);

        // 2. Передаем оба аргумента в конструктор
        $this->manager = new Manager(
            $this->diaryService,
            $accessContext
        );
    }

    public function testGetDiariesReturnsFormattedArray(): void
    {
        // 1. Готовим тестовые данные
        $year = 2024;
        $month = 3;

        $expectedType = ['id' => 'monthly', 'label' => 'Ежемесячный'];
        $expectedTitle = [
            'month' => 3,
            'year' => 2024,
        ];
        $expectedHeader = ['ID', 'Дата', 'Событие'];
        $expectedBody = [['id' => 1, 'date' => '2024-03-01', 'event' => 'Полив']];

        // 2. Настраиваем ожидания для DiaryService
        $this->diaryService->expects($this->once())
            ->method('getDiaryType')
            ->willReturn($expectedType);

        $this->diaryService->expects($this->once())
            ->method('getDiaryTitle')
            ->with($year, $month)
            ->willReturn($expectedTitle);

        $this->diaryService->expects($this->once())
            ->method('getDiaryHeader')
            ->with($year, $month)
            ->willReturn($expectedHeader);

        $this->diaryService->expects($this->once())
            ->method('getDiaryBody')
            ->willReturn($expectedBody);

        // 3. Выполняем метод
        $result = $this->manager->getDiaries($year, $month);

        // 4. Проверяем структуру и содержимое результата
        $this->assertIsArray($result);
        $this->assertArrayHasKey('diaryType', $result);
        $this->assertArrayHasKey('diaryTitle', $result);
        $this->assertArrayHasKey('tableHeader', $result);
        $this->assertArrayHasKey('tableBody', $result);

        $this->assertEquals($expectedType, $result['diaryType']);
        $this->assertEquals($expectedTitle, $result['diaryTitle']);
        $this->assertEquals($expectedHeader, $result['tableHeader']);
        $this->assertEquals($expectedBody, $result['tableBody']);
    }

    public function testGetDiariesHandlesNullParams(): void
    {
        // Проверяем, что null параметры корректно уходят в сервис
        $this->diaryService->expects($this->once())
            ->method('getDiaryTitle')
            ->with(null, null);

        $this->manager->getDiaries(null, null);
    }
}
