<?php

namespace Unit\Domain\Entity;

use App\Domain\Entity\Plant;
use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Offspring;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\ValueObject\Offspring\Phenology;
use App\Domain\ValueObject\Offspring\FruitMetrics;

#[CoversClass(Offspring::class)]
class OffspringTest extends TestCase
{
    private function createGroupMock(int $id = 0): Group
    {
        $mock = $this->createMock(Group::class);
        $mock->method('getId')->willReturn($id);
        return $mock;
    }

    private function createPlantMock(int $id = 0): Plant
    {
        $mock = $this->createMock(Plant::class);
        $mock->method('getId')->willReturn($id);
        return $mock;
    }

    #[Test]
    public function testConstructorInitializesCorrectly(): void
    {
        $group = $this->createGroupMock();
        $plant = $this->createPlantMock();

        $offspring = new Offspring($group, $plant);

        $this->assertSame($group, $offspring->getGroup());
        $this->assertSame($plant, $offspring->getPlant());

        // Проверяем, что VO создаются автоматически в конструкторе
        $this->assertInstanceOf(Phenology::class, $offspring->getPhenology());
        $this->assertInstanceOf(FruitMetrics::class, $offspring->getFruitMetrics());
        $this->assertNull($offspring->getComment());
    }

    #[Test]
    public function testRecordResultUpdatesData(): void
    {
        $offspring = new Offspring($this->createGroupMock(), $this->createPlantMock());

        // Мокаем или создаем реальные VO
        $phenology = $this->createMock(Phenology::class);
        $metrics = $this->createMock(FruitMetrics::class);
        $comment = 'Отличный урожай, сладкий вкус';

        $offspring->recordResult($phenology, $metrics, $comment);

        $this->assertSame($phenology, $offspring->getPhenology());
        $this->assertSame($metrics, $offspring->getFruitMetrics());
        $this->assertEquals($comment, $offspring->getComment());
    }

    #[Test]
    public function testMoveMethods(): void
    {
        // Создаем объекты с разными ID
        $initialGroup = $this->createGroupMock(1);
        $initialPlant = $this->createPlantMock(10);

        $offspring = new Offspring($initialGroup, $initialPlant);

        $newGroup = $this->createGroupMock(2); // ID другой, проверка в сущности пройдет
        $newPlant = $this->createPlantMock(20);

        $offspring->moveToGroup($newGroup);
        $offspring->moveToPlant($newPlant);

        $this->assertSame($newGroup, $offspring->getGroup(), 'Группа должна измениться');
        $this->assertSame($newPlant, $offspring->getPlant(), 'Растение должно измениться');
    }

    #[Test]
    public function testGetIdThrowsExceptionWhenNull(): void
    {
        $offspring = new Offspring($this->createGroupMock(), $this->createPlantMock());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Id of Entity App\Domain\Entity\Offspring is null.');

        $offspring->getId();
    }

    #[Test]
    public function testAttachmentsManagement(): void
    {
        $offspring = new Offspring($this->createGroupMock(), $this->createPlantMock());
        $attachments = ['photo1.jpg', 'photo2.jpg'];

        $offspring->setLoadedAttachments($attachments);
        $this->assertEquals($attachments, $offspring->getLoadedAttachments());
    }
}
