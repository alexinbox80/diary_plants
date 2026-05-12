<?php

namespace Unit\Domain\Entity;

use App\Domain\Entity\Pest;
use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Preparation\PreparationVolume;
use App\Domain\ValueObject\Preparation\PreparationDetails;

class PestTest extends TestCase
{
    private function createVolume(): PreparationVolume
    {
        return new PreparationVolume(100.0, 'ml');
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $group = $this->createMock(Group::class);
        $marker = $this->createMock(Marker::class);
        $volume = $this->createVolume();
        $details = new PreparationDetails('Против клеща', 'Концентрат');
        $title = 'Актеллик';

        $pest = new Pest($group, $marker, $title, $volume, $details);

        // Проверка свойств Pest
        $this->assertSame($group, $pest->getGroup());
        $this->assertSame($marker, $pest->getMarker());

        // Проверка свойств родителя Preparation
        $this->assertEquals($title, $pest->getTitle());
        $this->assertSame($volume, $pest->getVolume());
        $this->assertSame($details, $pest->getDetails());
    }

    public function testChangeFieldsWithMarker(): void
    {
        $group = $this->createMock(Group::class);
        $marker = $this->createMock(Marker::class);
        $pest = new Pest($group, $marker, 'Старое название', $this->createVolume());

        $newMarker = $this->createMock(Marker::class);
        $newVolume = new PreparationVolume(2.0, 'г');
        $newDetails = new PreparationDetails('Новая формула');

        // Убираем $newGroup из аргументов, так как метод его не принимает
        $pest->changeFieldsWithMarker($newMarker, 'Новое название', $newVolume, $newDetails);

        // Проверяем результат
        $this->assertSame($newMarker, $pest->getMarker());
        $this->assertEquals('Новое название', $pest->getTitle());
        $this->assertSame($newVolume, $pest->getVolume());

        // Группа должна остаться старой, так как метод её не меняет
        $this->assertSame($group, $pest->getGroup());
    }

    public function testGetIdThrowsExceptionWhenNull(): void
    {
        $pest = new Pest(
            $this->createMock(Group::class),
            $this->createMock(Marker::class),
            'Test Pest',
            $this->createVolume()
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Id of Entity App\Domain\Entity\Pest is null.');

        $pest->getId();
    }
}
