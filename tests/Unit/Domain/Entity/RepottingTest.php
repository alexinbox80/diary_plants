<?php

namespace Unit\Domain\Entity;

use App\Domain\Entity\Plant;
use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Repotting;
use App\Domain\ValueObject\Repotting\RepottingDetails;
use App\Domain\ValueObject\Enum\Repotting\PotMaterial;
use App\Domain\ValueObject\Enum\Repotting\RepottingType;

class RepottingTest extends TestCase
{
    private function createDetails(string $size = '12 cm'): RepottingDetails
    {
        // Исправлено под твой конструктор: Type, Material, StringSize
        return new RepottingDetails(
            RepottingType::POTTING_UP,
            PotMaterial::PLASTIC,
            $size
        );
    }

    public function testConstructorSetsFieldsCorrectly(): void
    {
        $group = $this->createMock(Group::class);
        $plant = $this->createMock(Plant::class);
        $date = new \DateTimeImmutable('2024-05-20');
        $details = $this->createDetails('14 cm');

        $repotting = new Repotting($group, $plant, $date, $details);

        $this->assertSame($group, $repotting->getGroup());
        $this->assertSame($plant, $repotting->getPlant());
        $this->assertSame($date, $repotting->getRepottedAt());
        $this->assertSame($details, $repotting->getDetails());
        $this->assertEquals('14 cm', $repotting->getDetails()->getPotSize());
    }

    public function testChangeFieldsUpdatesData(): void
    {
        $repotting = new Repotting(
            $this->createMock(Group::class),
            $this->createMock(Plant::class),
            new \DateTimeImmutable(),
            $this->createDetails()
        );

        $newGroup = $this->createMock(Group::class);
        $newPlant = $this->createMock(Plant::class);
        $newDate = new \DateTimeImmutable('tomorrow');
        $newDetails = new RepottingDetails(
            RepottingType::REPOT_FULL,
            PotMaterial::CERAMIC,
            '1.5 л'
        );

        $repotting->changeFields($newGroup, $newPlant, $newDate, $newDetails);

        $this->assertSame($newGroup, $repotting->getGroup());
        $this->assertSame($newPlant, $repotting->getPlant());
        $this->assertSame($newDate, $repotting->getRepottedAt());
        $this->assertEquals(RepottingType::REPOT_FULL, $repotting->getDetails()->getType());
        $this->assertEquals('1.5 л', $repotting->getDetails()->getPotSize());
    }

    public function testSettersAndGetters(): void
    {
        $repotting = new Repotting(
            $this->createMock(Group::class),
            $this->createMock(Plant::class),
            new \DateTimeImmutable(),
            $this->createDetails()
        );

        $repotting->setSubstrate('Lechuza Pon');
        $repotting->setComment('Strong roots');

        $this->assertEquals('Lechuza Pon', $repotting->getSubstrate());
        $this->assertEquals('Strong roots', $repotting->getComment());
    }

    public function testGetIdThrowsExceptionWhenNull(): void
    {
        $repotting = new Repotting(
            $this->createMock(Group::class),
            $this->createMock(Plant::class),
            new \DateTimeImmutable(),
            $this->createDetails()
        );

        // Проверка WebmozartAssert: ожидаем исключение, так как ID еще не присвоен базой
        $this->expectException(\InvalidArgumentException::class);
        $repotting->getId();
    }
}
