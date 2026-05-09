<?php

namespace Unit\Domain\Entity;

use DateTimeImmutable;
use App\Domain\Entity\Usage;
use App\Domain\Entity\Plant;
use App\Domain\Entity\Group;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
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

        $newGroup = $this->createGroupMock();
        $newPlant = $this->createPlantMock();
        $newTarget = $this->createTargetMock();
        $newDate = new DateTimeImmutable('tomorrow');
        $newComment = 'New comment';

        $usage->changeFields($newGroup, $newDate, $newPlant, $newTarget, $newComment);

        $this->assertSame($newGroup, $usage->getGroup());
        $this->assertSame($newPlant, $usage->getPlant());
        $this->assertSame($newTarget, $usage->getTarget());
        $this->assertSame($newDate, $usage->getUseDate());
        $this->assertEquals($newComment, $usage->getComment());
    }

    #[Test]
    public function testMoveToGroup(): void
    {
        // Создаем исходную группу с ID 1
        $oldGroup = $this->createGroupMock(1);

        $usage = new Usage(
            $oldGroup,
            new DateTimeImmutable(),
            $this->createPlantMock(),
            $this->createTargetMock()
        );

        // Создаем новую группу с ID 2
        $newGroup = $this->createGroupMock(2);

        $usage->moveToGroup($newGroup);

        // Теперь проверка (1 === 2) вернет false, и группа обновится
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
