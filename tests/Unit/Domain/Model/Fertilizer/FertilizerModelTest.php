<?php

namespace Unit\Domain\Model\Fertilizer;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Fertilizer;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Marker\MarkerModel;
use App\Domain\Model\Fertilizer\FertilizerModel;
use App\Domain\ValueObject\Preparation\PreparationVolume;
use App\Domain\ValueObject\Preparation\PreparationDetails;

class FertilizerModelTest extends TestCase
{
    /**
     * Проверка маппинга из Entity в Model
     */
    public function testFromEntityMapping(): void
    {
        // 1. Создаем моки зависимостей сущности
        $group = $this->createMock(Group::class);
        $group->method('getId')->willReturn(1);

        $marker = $this->createMock(Marker::class);
        $marker->method('getId')->willReturn(5);

        $volume = $this->createMock(PreparationVolume::class);
        $volume->method('getAmount')->willReturn(500);
        $volume->method('getApplicationRate')->willReturn('10 мл на 1 л');

        $details = $this->createMock(PreparationDetails::class);
        $details->method('getManufacturer')->willReturn('Agricola');
        $details->method('getDescription')->willReturn('Универсальное');
        $details->method('getComment')->willReturn('Хороший результат');

        $fertilizer = $this->createMock(Fertilizer::class);
        $fertilizer->method('getId')->willReturn(100);
        $fertilizer->method('getGroup')->willReturn($group);
        $fertilizer->method('getMarker')->willReturn($marker);
        $fertilizer->method('getTitle')->willReturn('Удобрение №1');
        $fertilizer->method('getVolume')->willReturn($volume);
        $fertilizer->method('getDetails')->willReturn($details);
        $fertilizer->method('getCreatedAt')->willReturn(new DateTimeImmutable('2024-01-01 10:00:00'));
        $fertilizer->method('getUpdatedAt')->willReturn(new DateTimeImmutable('2024-01-01 11:00:00'));

        // 2. Вызов метода
        $model = FertilizerModel::fromEntity($fertilizer);

        // 3. Проверки
        $this->assertEquals(100, $model->getId());
        $this->assertEquals(1, $model->getGroupId());
        $this->assertEquals('Удобрение №1', $model->getTitle());
        $this->assertEquals(500, $model->getAmount());
        $this->assertEquals('Agricola', $model->getManufacturer()); // Теперь порядок верный
        $this->assertEquals('10 мл на 1 л', $model->getApplicationRate());
    }

    /**
     * Проверка формирования массива для фронтенда и конвертации таймзоны
     */
    public function testToArrayFormatting(): void
    {
        $dateUtc = new DateTimeImmutable('2024-01-01 12:00:00', new DateTimeZone('UTC'));

        $groupModel = $this->createMock(GroupModel::class);
        $groupModel->method('getTitle')->willReturn('Комнатные');

        $markerModel = $this->createMock(MarkerModel::class);
        $markerModel->method('getLetter')->willReturn('F');
        $markerModel->method('getColor')->willReturn('#00FF00');

        $model = new FertilizerModel(
            id: 1,
            groupId: 10,
            markerId: 5,
            title: 'Тест',
            amount: 250,
            manufacturer: 'Bona Forte',
            applicationRate: '5 мл',
            description: null,
            comment: null,
            group: $groupModel,
            marker: $markerModel,
            createdAt: $dateUtc,
            updatedAt: $dateUtc
        );

        $result = $model->toArray();

        $this->assertEquals('Комнатные', $result['group_title']);
        $this->assertEquals('F', $result['marker_letter']);
        $this->assertEquals('01.01.2024 15:00:00', $result['created_at']);
    }

    public function testGetTableHeaderRu(): void
    {
        $headers = FertilizerModel::getTableHeaderRu();
        $this->assertArrayHasKey('application_rate', $headers);
        $this->assertEquals('table.fertilizer.header.application_rate', $headers['application_rate']);
    }
}
