<?php

namespace Unit\Domain\Entity;

use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Attachment;
use App\Domain\ValueObject\Attachment\FileInfo;
use App\Domain\ValueObject\Attachment\DisplaySettings;
use App\Domain\ValueObject\Attachment\AttachableReference;

class AttachmentTest extends TestCase
{
    private function createDisplaySettings(): DisplaySettings
    {
        // Предполагаем стандартный конструктор: title, description, alt, isShown
        return new DisplaySettings('Photo', 'Desc', 'Alt text', true);
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $group = $this->createMock(Group::class);
        $displaySettings = $this->createDisplaySettings();

        $attachment = new Attachment($group, $displaySettings);

        $this->assertSame($group, $attachment->getGroup());
        $this->assertSame($displaySettings, $attachment->getDisplaySettings());

        // Проверяем, что пустые VO инициализированы в конструкторе
        $this->assertInstanceOf(FileInfo::class, $attachment->getFileInfo());
        $this->assertInstanceOf(AttachableReference::class, $attachment->getTarget());
    }

    public function testUpdateMethods(): void
    {
        $group = $this->createMock(Group::class);
        $attachment = new Attachment($group, $this->createDisplaySettings());

        // Тестируем обновление DisplaySettings
        $newDisplay = new DisplaySettings('New Title', 'New Desc', 'New Alt', false);
        $attachment->updateDisplaySettings($newDisplay);
        $this->assertSame($newDisplay, $attachment->getDisplaySettings());

        // Тестируем обновление FileInfo
        $fileInfo = $this->createMock(FileInfo::class);
        $attachment->updateFileInfo($fileInfo);
        $this->assertSame($fileInfo, $attachment->getFileInfo());

        // Тестируем обновление Target (AttachableReference)
        $target = $this->createMock(AttachableReference::class);
        $attachment->updateTarget($target);
        $this->assertSame($target, $attachment->getTarget());
    }

    public function testMoveToGroup(): void
    {
        $oldGroup = $this->createMock(Group::class);
        $newGroup = $this->createMock(Group::class);
        $attachment = new Attachment($oldGroup, $this->createDisplaySettings());

        $attachment->moveToGroup($newGroup);
        $this->assertSame($newGroup, $attachment->getGroup());
    }

    public function testGetIdThrowsExceptionWhenNull(): void
    {
        $attachment = new Attachment(
            $this->createMock(Group::class),
            $this->createDisplaySettings()
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Id of Entity App\Domain\Entity\Attachment is null.');

        $attachment->getId();
    }
}
