<?php

namespace Unit\Domain\Model\Pest;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Pest;
use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use PHPUnit\Framework\TestCase;
use App\Domain\Model\Pest\PestModel;
use App\Domain\Model\Group\GroupModel;
use App\Domain\ValueObject\Preparation\PreparationVolume;
use App\Domain\ValueObject\Preparation\PreparationDetails;

class PestModelTest extends TestCase
{
    /**
     * Тест маппинга из сущности Pest в модель
     */
    public function testFromEntityMapping(): void
    {
        $group = $this->createMock(Group::class);
        $group->method('getId')->willReturn(1);

        $marker = $this->createMock(Marker::class);
        $marker->method('getId')->willReturn(5);

        $volume = $this->createMock(PreparationVolume::class);
        $volume->method('getAmount')->willReturn(100);
        $volume->method('getApplicationRate')->willReturn('2 мл/л');

        $details = $this->createMock(PreparationDetails::class);
        $details->method('getManufacturer')->willReturn('Bayer');
        $details->method('getDescription')->willReturn('От трипса');
        $details->method('getComment')->willReturn('Эффективно');

        $pest = $this->createMock(Pest::class);
        $pest->method('getId')->willReturn(50);
        $pest->method('getGroup')->willReturn($group);
        $pest->method('getMarker')->willReturn($marker);
        $pest->method('getTitle')->willReturn('Конфидор');
        $pest->method('getVolume')->willReturn($volume);
        $pest->method('getDetails')->willReturn($details);
        $pest->method('getCreatedAt')->willReturn(new DateTimeImmutable('2024-01-01 10:00:00'));
        $pest->method('getUpdatedAt')->willReturn(new DateTimeImmutable('2024-01-01 11:00:00'));

        $model = PestModel::fromEntity($pest);

        $this->assertEquals(50, $model->getId());
        $this->assertEquals('Конфидор', $model->getTitle());
        $this->assertEquals('Bayer', $model->getManufacturer());
        $this->assertEquals(100, $model->getAmount());
    }

    /**
     * Тест преобразования в массив и форматирования дат (Таймзона Москва)
     */
    public function testToArrayFormatting(): void
    {
        $dateUtc = new DateTimeImmutable('2024-01-01 12:00:00', new DateTimeZone('UTC'));

        $groupModel = $this->createMock(GroupModel::class);
        $groupModel->method('getTitle')->willReturn('Теплица');

        $model = new PestModel(
            id: 1,
            groupId: 10,
            markerId: 5,
            title: 'Фитоверм',
            amount: 10,
            manufacturer: 'Фармбиомед',
            applicationRate: null,
            description: null,
            comment: null,
            group: $groupModel,
            marker: null,
            createdAt: $dateUtc,
            updatedAt: $dateUtc
        );

        $result = $model->toArray();

        $this->assertEquals('Теплица', $result['group_title']);
        $this->assertEquals('Фармбиомед', $result['manufacturer']);
        // UTC 12:00 -> MSK 15:00
        $this->assertEquals('01.01.2024 15:00:00', $result['created_at']);
    }

    public function testGetTableHeaderRu(): void
    {
        $headers = PestModel::getTableHeaderRu();
        $this->assertArrayHasKey('marker_letter', $headers);
        $this->assertEquals('Количество', $headers['amount']);
    }
}
