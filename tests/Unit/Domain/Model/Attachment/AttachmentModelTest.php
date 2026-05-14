<?php

namespace Unit\Domain\Model\Attachment;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Attachment;
use App\Domain\ValueObject\Attachment\FileInfo;
use App\Domain\Model\Attachment\AttachmentModel;
use App\Domain\ValueObject\Attachment\DisplaySettings;
use App\Domain\ValueObject\Attachment\AttachableReference;
use App\Domain\ValueObject\Enum\Attachment\AttachableType;

class AttachmentModelTest extends TestCase
{
    /**
     * Тестируем создание модели из сущности (маппинг)
     */
    public function testFromEntityMapping(): void
    {
        // 1. Подготовка моков зависимостей сущности
        $group = $this->createMock(Group::class);
        $group->method('getId')->willReturn(10);

        // Реальный объект DisplaySettings (не final)
        $displaySettings = new DisplaySettings(
            title: 'Инжир в горшке',
            description: 'Вид после пересадки',
            alt: 'Фикус карика',
            isShown: true
        );

        $fileInfo = $this->createMock(FileInfo::class);
        $fileInfo->method('getFilename')->willReturn('fig_tree.jpg');
        $fileInfo->method('getPath')->willReturn('attachments/plant/2/');
        $fileInfo->method('getMimeType')->willReturn('image/jpeg');
        $fileInfo->method('getFileDate')->willReturn(new DateTimeImmutable('2024-05-01'));

        $target = $this->createMock(AttachableReference::class);
        $target->method('getAttachableId')->willReturn(2);
        // Предполагаем, что Enum возвращает объект, у которого есть свойство value = 'plant'
        $target->method('getAttachableType')->willReturn(AttachableType::PLANT);

        // 2. Сборка основного мока Attachment
        $attachment = $this->createMock(Attachment::class);
        $attachment->method('getId')->willReturn(1);
        $attachment->method('getGroup')->willReturn($group);
        $attachment->method('getDisplaySettings')->willReturn($displaySettings);
        $attachment->method('getFileInfo')->willReturn($fileInfo);
        $attachment->method('getTarget')->willReturn($target);
        $attachment->method('getCreatedAt')->willReturn(new DateTimeImmutable('2024-01-01 10:00:00', new DateTimeZone('UTC')));
        $attachment->method('getUpdatedAt')->willReturn(new DateTimeImmutable('2024-01-01 11:00:00', new DateTimeZone('UTC')));

        // 3. Выполнение маппинга
        $model = AttachmentModel::fromEntity($attachment);

        // 4. Проверки
        $this->assertInstanceOf(AttachmentModel::class, $model);
        $this->assertEquals(1, $model->getId());
        $this->assertEquals(10, $model->getGroupId());
        $this->assertEquals('Инжир в горшке', $model->getTitle());
        $this->assertEquals('plant', $model->getAttachableType()); // Исправлено на нижний регистр
        $this->assertTrue($model->isShown());
    }

    /**
     * Тестируем преобразование модели в массив для фронтенда/API
     */
    public function testToArrayFormatting(): void
    {
        $createdAt = new DateTimeImmutable('2024-01-01 12:00:00', new DateTimeZone('UTC'));
        $fileDate = new DateTimeImmutable('2024-05-01');

        // Передаем абсолютно все аргументы в том порядке, в котором они в конструкторе,
        // либо заполняем nullable-поля явно как null.
        $model = new AttachmentModel(
            id: 1,
            groupId: 10,
            isShown: true,
            alt: 'Альтернативный текст',
            title: 'Заголовок фото',
            fileDate: $fileDate,
            group: null,           // Аргумент #7 (был пропущен)
            filename: 'test_image.png',
            path: 'uploads/plants/',
            mimeType: 'image/png',
            description: 'Описание фотографии',
            attachableId: null,
            attachableType: 'plant',
            attachable: null,
            createdAt: $createdAt, // Теперь эти обязательные поля
            updatedAt: $createdAt  // будут приняты корректно
        );

        $result = $model->toArray();

        $this->assertEquals('uploads/plants/test_image.png', $result['img_tag']);
        $this->assertEquals('Да', $result['is_shown_label']);
        $this->assertEquals('01.01.2024 15:00:00', $result['created_at']);
        $this->assertEquals('01.05.2024', $result['file_date']);
    }

    /**
     * Проверка заголовков таблицы
     */
    public function testTableHeaderRuKeys(): void
    {
        $headers = AttachmentModel::getTableHeaderRu();

        $this->assertIsArray($headers);
        $this->assertArrayHasKey('img_tag', $headers);
        $this->assertEquals('table.attachment.header.img_tag', $headers['img_tag']);
        $this->assertArrayHasKey('is_shown_label', $headers);
    }
}

