<?php

namespace Unit\Domain\Entity;

use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use App\Domain\Entity\Watering;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Enum\Watering\WaterType;
use App\Domain\ValueObject\Watering\WateringDetails;
use App\Domain\ValueObject\Enum\Watering\WateringMethod;

class WateringTest extends TestCase
{
    private function createGroupMock(): Group
    {
        return $this->createMock(Group::class);
    }

    private function createMarkerMock(): Marker
    {
        return $this->createMock(Marker::class);
    }

    private function createDetails(): WateringDetails
    {
        // Передаем int, Enum и опционально строку температуры
        return new WateringDetails(
            amount: 500,
            type: WaterType::FILTERED,
            method: WateringMethod::TOP,
            temperature: '22.5'
        );
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $group = $this->createGroupMock();
        $marker = $this->createMarkerMock();
        $details = $this->createDetails();

        $watering = new Watering($group, $marker, $details);

        $this->assertSame($group, $watering->getGroup());
        $this->assertSame($marker, $watering->getMarker());
        $this->assertSame($details, $watering->getDetails());

        $this->assertEquals(500, $watering->getDetails()->getAmount());
        $this->assertEquals(22.5, $watering->getDetails()->getTemperature());
    }

    public function testUpdateFields(): void
    {
        // 1. Создаем моки групп с РАЗНЫМИ ID
        $group = $this->createGroupMock();
        $group->method('getId')->willReturn(1);

        $newGroup = $this->createGroupMock();
        $newGroup->method('getId')->willReturn(2);

        // 2. Создаем мок маркера и настраиваем ожидания для moveToGroup
        $marker = $this->createMarkerMock();
        $marker->expects($this->once())
            ->method('moveToGroup')
            ->with($newGroup)
            ->willReturn($marker); // Возвращает сам себя (Fluent Interface)

        $watering = new Watering(
            $group,
            $marker,
            $this->createDetails()
        );

        $newMarker = $this->createMarkerMock();
        $newDetails = new WateringDetails(1000, WaterType::RAIN, WateringMethod::BOTTOM);

        // 3. Выполняем операции обновления
        $watering->moveToGroup($newGroup);
        $watering->changeFields($newMarker, $newDetails);

        // 4. Проверяем результаты
        $this->assertSame($newGroup, $watering->getGroup());
        $this->assertSame($newMarker, $watering->getMarker());
        $this->assertSame($newDetails, $watering->getDetails());
        $this->assertEquals(WaterType::RAIN, $watering->getDetails()->getType());
    }

    public function testGetIdThrowsExceptionWhenNull(): void
    {
        $watering = new Watering(
            $this->createGroupMock(),
            $this->createMarkerMock(),
            $this->createDetails()
        );

        $this->expectException(\InvalidArgumentException::class);
        $watering->getId();
    }
}
