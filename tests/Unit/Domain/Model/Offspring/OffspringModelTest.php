<?php

namespace Unit\Domain\Model\Offspring;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Plant;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Offspring;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Model\Offspring\OffspringModel;
use App\Domain\ValueObject\Offspring\Phenology;
use App\Domain\Model\Attachment\AttachmentModel;
use App\Domain\ValueObject\Offspring\FruitMetrics;

class OffspringModelTest extends TestCase
{
    /**
     * Тест преобразования модели в массив (toArray)
     */
    public function testToArrayFormatting(): void
    {
        // 0. Инициализация базового времени (UTC)
        $now = new DateTimeImmutable('2024-01-01 12:00:00', new DateTimeZone('UTC'));

        // 1. Мокаем Value Objects сущности Offspring
        $phenology = $this->createMock(Phenology::class);
        $phenology->method('getFruitingDate')->willReturn(new DateTimeImmutable('2024-05-10'));
        $phenology->method('getFloweringDate')->willReturn(null);

        $metrics = $this->createMock(FruitMetrics::class);
        $metrics->method('getMass')->willReturn(150);
        $metrics->method('getQuantity')->willReturn(1);
        $metrics->method('getColor')->willReturn('Желтый');
        $metrics->method('getFlavor')->willReturn('Кислый');

        // 2. Мокаем связи (Entity)
        $groupEntity = $this->createMock(Group::class);
        $groupEntity->method('getId')->willReturn(10);

        $plantEntity = $this->createMock(Plant::class);
        $plantEntity->method('getId')->willReturn(5);

        // 3. Мокаем основную сущность Offspring
        $offspring = $this->createMock(Offspring::class);
        $offspring->method('getId')->willReturn(1);
        $offspring->method('getGroup')->willReturn($groupEntity);
        $offspring->method('getPlant')->willReturn($plantEntity);
        $offspring->method('getPhenology')->willReturn($phenology);
        $offspring->method('getFruitMetrics')->willReturn($metrics);
        $offspring->method('getComment')->willReturn('Тестовый комментарий');
        $offspring->method('getCreatedAt')->willReturn($now);
        $offspring->method('getUpdatedAt')->willReturn($now);

        // 4. Готовим связанные модели (Model)
        $plantModel = $this->createMock(PlantModel::class);
        $plantModel->method('getTitle')->willReturn('Лимон Мейера');

        $validAttachment = $this->createMock(AttachmentModel::class);
        $validAttachment->method('getMimeType')->willReturn('image/jpeg');
        $validAttachment->method('toArray')->willReturn(['url' => 'image.jpg']);

        // 5. Вызов тестируемого метода fromEntity
        $model = OffspringModel::fromEntity(
            $offspring,
            [$validAttachment],
            $plantModel
        );

        // 6. Проверки состояния модели (соответствие переданным данным)
        $this->assertEquals(1, $model->getId());
        $this->assertEquals(10, $model->getGroupId());
        $this->assertEquals('Лимон Мейера', $model->getPlant()?->getTitle());

        // 7. Проверка форматирования в массив (toArray)
        $result = $model->toArray();

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Лимон Мейера', $result['plant_title']);
        $this->assertEquals('10.05.2024', $result['fruiting_date']);

        // Проверка трансформации таймзоны (UTC 12:00 превращается в Europe/Moscow 15:00)
        $this->assertEquals('01.01.2024 15:00:00', $result['created_at']);

        // Проверка корректности фильтрации вложений в img_gallery
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
        $this->assertArrayHasKey('quantity', $headers);
    }
}
