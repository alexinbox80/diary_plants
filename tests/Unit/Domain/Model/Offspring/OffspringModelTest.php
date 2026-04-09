<?php

namespace Unit\Domain\Model\Offspring;

use DateTimeZone;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Model\Offspring\OffspringModel;
use App\Domain\Model\Attachment\AttachmentModel;

class OffspringModelTest extends TestCase
{
    /**
     * Тест преобразования модели в массив (toArray)
     */
    public function testToArrayFormatting(): void
    {
        $createdAt = new DateTimeImmutable('2024-01-01 12:00:00', new DateTimeZone('UTC'));
        $fruitingDate = new DateTimeImmutable('2024-05-10');

        $plantModel = $this->createMock(PlantModel::class);
        $plantModel->method('getTitle')->willReturn('Лимон Мейера');

        $validAttachment = $this->createMock(AttachmentModel::class);
        $validAttachment->method('getMimeType')->willReturn('image/jpeg');
        $validAttachment->method('toArray')->willReturn(['url' => 'image.jpg']);

        $model = new OffspringModel(
            id: 1,
            groupId: 10,
            plantId: 5,
            attachment: [$validAttachment],
            fruitingDate: $fruitingDate,
            floweringDate: null,
            mass: 150,
            color: 'Желтый',
            flavor: 'Кислый',
            quantity: 1,
            comment: null,
            plant: $plantModel,
            group: null,
            createdAt: $createdAt,
            updatedAt: $createdAt
        );

        $result = $model->toArray();

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Лимон Мейера', $result['plant_title']);
        $this->assertEquals('10.05.2024', $result['fruiting_date']);
        $this->assertEquals('01.01.2024 15:00:00', $result['created_at']);
        $this->assertCount(1, $result['img_gallery']);
    }

    /**
     * Тест заголовков таблицы
     */
    public function testGetTableHeaderRu(): void
    {
        $headers = OffspringModel::getTableHeaderRu();
        $this->assertEquals('Масса гр.', $headers['mass']);
        $this->assertEquals('Дата сбора', $headers['fruiting_date']);
    }
}
