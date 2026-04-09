<?php

namespace Unit\Domain\Entity;

use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Fertilizer;
use App\Domain\ValueObject\Preparation\PreparationVolume;
use App\Domain\ValueObject\Preparation\PreparationDetails;

class FertilizerTest extends TestCase
{
    private function createVolume(): PreparationVolume
    {
        // Предполагаем конструктор (значение, единица измерения)
        return new PreparationVolume(500.0, 'ml');
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $group = $this->createMock(Group::class);
        $marker = $this->createMock(Marker::class);
        $volume = $this->createVolume();
        $details = new PreparationDetails('NPK 5-5-5', 'Organic');
        $title = 'BioGrow';

        $fertilizer = new Fertilizer($group, $marker, $title, $volume, $details);

        // Проверка полей сущности Fertilizer
        $this->assertSame($group, $fertilizer->getGroup());
        $this->assertSame($marker, $fertilizer->getMarker());

        // Проверка полей родительского класса Preparation
        $this->assertEquals($title, $fertilizer->getTitle());
        $this->assertSame($volume, $fertilizer->getVolume());
        $this->assertSame($details, $fertilizer->getDetails());
    }

    public function testChangeFieldsWithMarker(): void
    {
        $group = $this->createMock(Group::class);
        $marker = $this->createMock(Marker::class);
        $fertilizer = new Fertilizer($group, $marker, 'Old Title', $this->createVolume());

        $newGroup = $this->createMock(Group::class);
        $newMarker = $this->createMock(Marker::class);
        $newVolume = new PreparationVolume(1.0, 'll');
        $newDetails = new PreparationDetails('New Formula');

        $fertilizer->changeFieldsWithMarker($newGroup, $newMarker, 'New Title', $newVolume, $newDetails);

        $this->assertSame($newGroup, $fertilizer->getGroup());
        $this->assertSame($newMarker, $fertilizer->getMarker());
        $this->assertEquals('New Title', $fertilizer->getTitle());
        $this->assertSame($newVolume, $fertilizer->getVolume());
    }

    public function testGetIdThrowsExceptionWhenNull(): void
    {
        $fertilizer = new Fertilizer(
            $this->createMock(Group::class),
            $this->createMock(Marker::class),
            'Test',
            $this->createVolume()
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Id of Entity App\Domain\Entity\Fertilizer is null.');

        $fertilizer->getId();
    }
}
