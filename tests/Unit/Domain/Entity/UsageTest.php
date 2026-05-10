<?php

namespace Unit\Domain\Entity;

use DateTimeImmutable;
use App\Domain\Entity\Usage;
use App\Domain\Entity\Plant;
use App\Domain\Entity\Group;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Attachment;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\ValueObject\Usage\AttachableReference;

#[CoversClass(Usage::class)]
class UsageTest extends TestCase
{
    private function createGroupMock(int $id = 0): Group
    {
        $mock = $this->createMock(Group::class);
        $mock->method('getId')->willReturn($id);
        return $mock;
    }

    private function createPlantMock(): Plant
    {
        return $this->createMock(Plant::class);
    }

    private function createTargetMock(): AttachableReference
    {
        return $this->createMock(AttachableReference::class);
    }

    #[Test]
    public function testConstructorInitializesCorrectly(): void
    {
        $group = $this->createGroupMock();
        $plant = $this->createPlantMock();
        $target = $this->createTargetMock();
        $date = new DateTimeImmutable('2024-01-01');
        $comment = 'Обработка от клеща';

        $usage = new Usage($group, $date, $plant, $target, $comment);

        $this->assertSame($group, $usage->getGroup());
        $this->assertSame($plant, $usage->getPlant());
        $this->assertSame($target, $usage->getTarget());
        $this->assertSame($date, $usage->getUseDate());
        $this->assertEquals($comment, $usage->getComment());
    }

    #[Test]
    public function testChangeFieldsUpdatesData(): void
    {
        $usage = new Usage(
            $this->createGroupMock(),
            new DateTimeImmutable(),
            $this->createPlantMock(),
            $this->createTargetMock()
        );

        $newPlant = $this->createPlantMock();
        $newTarget = $this->createTargetMock();
        $newDate = new DateTimeImmutable('tomorrow');
        $newComment = 'New comment';

        $usage->changeFields($newDate, $newPlant, $newTarget, $newComment);

        $this->assertSame($newPlant, $usage->getPlant());
        $this->assertSame($newTarget, $usage->getTarget());
        $this->assertSame($newDate, $usage->getUseDate());
        $this->assertEquals($newComment, $usage->getComment());
    }

    #[Test]
    public function testMoveToGroup(): void
    {
        // 1. Создаем исходную группу
        $oldGroup = $this->createGroupMock(1);

        // 2. Создаем Usage
        $usage = new Usage(
            $oldGroup,
            new DateTimeImmutable(),
            $this->createPlantMock(),
            $this->createTargetMock()
        );

        // 3. Создаем мок вложения и передаем его в Usage
        $attachmentMock = $this->createMock(Attachment::class);

        // Если в Usage есть метод setLoadedAttachment:
        $usage->setLoadedAttachment($attachmentMock);

        // 4. Создаем новую группу
        $newGroup = $this->createGroupMock(2);

        // 5. Теперь moveToGroup не упадет на null
        $usage->moveToGroup($newGroup);

        $this->assertSame($newGroup, $usage->getGroup(), 'Группа в Usage должна обновиться');
    }

    #[Test]
    public function testGetIdThrowsExceptionWhenNull(): void
    {
        $usage = new Usage(
            $this->createGroupMock(),
            new DateTimeImmutable(),
            $this->createPlantMock(),
            $this->createTargetMock()
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Id of Entity App\Domain\Entity\Usage is null.');

        $usage->getId();
    }
}
