<?php

namespace Unit\Domain\Entity;

use App\Domain\Entity\Usage;
use App\Domain\Entity\Plant;
use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Usage\AttachableReference;

class UsageTest extends TestCase
{
    private function createGroupMock(): Group
    {
        return $this->createMock(Group::class);
    }

    private function createPlantMock(): Plant
    {
        return $this->createMock(Plant::class);
    }

    private function createTargetMock(): AttachableReference
    {
        return $this->createMock(AttachableReference::class);
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $group = $this->createGroupMock();
        $plant = $this->createPlantMock();
        $target = $this->createTargetMock();
        $date = new \DateTimeImmutable('2024-01-01');
        $comment = 'Обработка от клеща';

        $usage = new Usage($group, $date, $plant, $target, $comment);

        $this->assertSame($group, $usage->getGroup());
        $this->assertSame($plant, $usage->getPlant());
        $this->assertSame($target, $usage->getTarget());
        $this->assertSame($date, $usage->getUseDate());
        $this->assertEquals($comment, $usage->getComment());
    }

    public function testChangeFieldsUpdatesData(): void
    {
        $usage = new Usage(
            $this->createGroupMock(),
            new \DateTimeImmutable(),
            $this->createPlantMock(),
            $this->createTargetMock()
        );

        $newGroup = $this->createGroupMock();
        $newPlant = $this->createPlantMock();
        $newTarget = $this->createTargetMock();
        $newDate = new \DateTimeImmutable('tomorrow');
        $newComment = 'New comment';

        $usage->changeFields($newGroup, $newDate, $newPlant, $newTarget, $newComment);

        $this->assertSame($newGroup, $usage->getGroup());
        $this->assertSame($newPlant, $usage->getPlant());
        $this->assertSame($newTarget, $usage->getTarget());
        $this->assertSame($newDate, $usage->getUseDate());
        $this->assertEquals($newComment, $usage->getComment());
    }

    public function testMoveToGroup(): void
    {
        $usage = new Usage(
            $this->createGroupMock(),
            new \DateTimeImmutable(),
            $this->createPlantMock(),
            $this->createTargetMock()
        );

        $newGroup = $this->createGroupMock();
        $usage->moveToGroup($newGroup);

        $this->assertSame($newGroup, $usage->getGroup());
    }

    public function testGetIdThrowsExceptionWhenNull(): void
    {
        $usage = new Usage(
            $this->createGroupMock(),
            new \DateTimeImmutable(),
            $this->createPlantMock(),
            $this->createTargetMock()
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Id of Entity App\Domain\Entity\Usage is null.');

        $usage->getId();
    }
}
