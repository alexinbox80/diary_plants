<?php

namespace Unit\Domain\Service;

use App\Domain\Entity\Marker;
use App\Domain\Entity\Watering;
use PHPUnit\Framework\TestCase;
use App\Domain\Service\PestService;
use App\Domain\Service\PlantService;
use App\Domain\Service\DiaryService;
use App\Domain\Service\WateringService;
use App\Domain\Service\StimulantService;
use App\Domain\Service\FertilizerService;
use PHPUnit\Framework\MockObject\MockObject;
use App\Domain\ValueObject\Enum\Usage\AttachableType;

class DiaryServiceTest extends TestCase
{
    private PlantService&MockObject $plantService;
    private WateringService&MockObject $wateringService;
    private PestService&MockObject $pestService;
    private FertilizerService&MockObject $fertilizerService;
    private StimulantService&MockObject $stimulantService;
    private DiaryService $service;

    protected function setUp(): void
    {
        $this->plantService = $this->createMock(PlantService::class);
        $this->wateringService = $this->createMock(WateringService::class);
        $this->pestService = $this->createMock(PestService::class);
        $this->fertilizerService = $this->createMock(FertilizerService::class);
        $this->stimulantService = $this->createMock(StimulantService::class);

        $this->service = new DiaryService(
            $this->plantService,
            $this->wateringService,
            $this->pestService,
            $this->fertilizerService,
            $this->stimulantService
        );
    }

    public function testGetDiaryTypeGroupsDataCorrectly(): void
    {
        // 1. Мокаем сущность Marker
        $markerEntity = $this->createMock(Marker::class);

        // ИСПРАВЛЕНО: возвращаем Enum вместо строки
        $markerEntity->method('getType')->willReturn(AttachableType::WATERING);

        $markerEntity->method('getLetter')->willReturn('W');
        $markerEntity->method('getColor')->willReturn('#0000FF');

        // 2. Мокаем объект использования (напр. Watering)
        $usageMock = $this->createMock(Watering::class);
        $usageMock->method('getId')->willReturn(1);
        $usageMock->method('getDescription')->willReturn('Daily watering');
        $usageMock->method('getMarker')->willReturn($markerEntity);

        // 3. Настраиваем сервисы
        $this->wateringService->method('getWateringsForDairy')->willReturn([$usageMock]);
        $this->pestService->method('getPestsForDairy')->willReturn([]);
        $this->fertilizerService->method('getFertilizersForDairy')->willReturn([]);
        $this->stimulantService->method('getStimulantsForDairy')->willReturn([]);

        $result = $this->service->getDiaryType();

        // 4. Проверки
        // Обратите внимание: в коде сервиса $marker->getMarker()->getType()
        // превращается в ключ массива. Если getType() возвращает Enum,
        // PHP автоматически использует его ->value или выдаст ошибку, если ключ не строка.
        $this->assertCount(1, $result['watering']);
        $this->assertEquals('W', $result['watering'][0]['letter']);
        $this->assertEquals('watering', $result['watering'][0]['type']);
    }

    public function testGetDiaryHeaderReturnsCorrectDaysCount(): void
    {
        // Тестируем февраль 2024 года (високосный год, 29 дней)
        $result = $this->service->getDiaryHeader(2024, 2);

        $this->assertCount(29, $result['tableHeader']);
        $this->assertEquals(1, $result['tableHeader'][0]['num_day']);
        $this->assertEquals('Чт', $result['tableHeader'][0]['name_day']); // 1 фев 2024 - четверг
    }

    public function testGetDiaryTitleUsesDefaults(): void
    {
        $result = $this->service->getDiaryTitle();

        $this->assertEquals(date('Y'), $result['year']);
        $this->assertEquals(date('n'), $result['month']);
    }
}
