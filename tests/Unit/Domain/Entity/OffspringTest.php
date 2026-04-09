<?php

namespace Unit\Domain\Entity;

use App\Domain\Entity\Plant;
use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Offspring;
use App\Domain\ValueObject\Offspring\Phenology;
use App\Domain\ValueObject\Offspring\FruitMetrics;

class OffspringTest extends TestCase
{
    private function createGroupMock(): Group
    {
        return $this->createMock(Group::class);
    }

    private function createPlantMock(): Plant
    {
        return $this->createMock(Plant::class);
    }

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

    public function testMoveMethods(): void
    {
        $offspring = new Offspring($this->createGroupMock(), $this->createPlantMock());

        $newGroup = $this->createGroupMock();
        $newPlant = $this->createPlantMock();

        $offspring->moveToGroup($newGroup);
        $offspring->moveToPlant($newPlant);

        $this->assertSame($newGroup, $offspring->getGroup());
        $this->assertSame($newPlant, $offspring->getPlant());
    }

    public function testGetIdThrowsExceptionWhenNull(): void
    {
        $offspring = new Offspring($this->createGroupMock(), $this->createPlantMock());

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Id of Entity App\Domain\Entity\Offspring is null.');

        $offspring->getId();
    }

    public function testAttachmentsManagement(): void
    {
        $offspring = new Offspring($this->createGroupMock(), $this->createPlantMock());
        $attachments = ['photo1.jpg', 'photo2.jpg'];

        $offspring->setLoadedAttachments($attachments);
        $this->assertEquals($attachments, $offspring->getLoadedAttachments());
    }
}
