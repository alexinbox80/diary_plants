<?php

namespace Unit\Domain\Entity;

use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Stimulant;
use App\Domain\ValueObject\Preparation\PreparationVolume;
use App\Domain\ValueObject\Preparation\PreparationDetails;

class StimulantTest extends TestCase
{
    private function createVolume(): PreparationVolume
    {
        return new PreparationVolume(50.0, 'ml');
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $group = $this->createMock(Group::class);
        $marker = $this->createMock(Marker::class);
        $volume = $this->createVolume();
        $details = new PreparationDetails('Стимулятор корнеобразования', 'Гормональный');
        $title = 'Корневин';

        $stimulant = new Stimulant($group, $marker, $title, $volume, $details);

        // Проверка полей сущности Stimulant
        $this->assertSame($group, $stimulant->getGroup());
        $this->assertSame($marker, $stimulant->getMarker());

        // Проверка полей родительского класса Preparation
        $this->assertEquals($title, $stimulant->getTitle());
        $this->assertSame($volume, $stimulant->getVolume());
        $this->assertSame($details, $stimulant->getDetails());
    }

    public function testChangeFieldsWithMarker(): void
    {
        $group = $this->createMock(Group::class);
        $marker = $this->createMock(Marker::class);
        $stimulant = new Stimulant($group, $marker, 'Эпин', $this->createVolume());

        $newMarker = $this->createMock(Marker::class);
        $newVolume = new PreparationVolume(1.0, 'гr');
        $newDetails = new PreparationDetails('Антистрессовый препарат');

        $stimulant->changeFieldsWithMarker($newMarker, 'Циркон', $newVolume, $newDetails);

        $this->assertSame($newMarker, $stimulant->getMarker());
        $this->assertEquals('Циркон', $stimulant->getTitle());
        $this->assertSame($newVolume, $stimulant->getVolume());
        $this->assertSame($newDetails, $stimulant->getDetails());
    }

    public function testGetIdThrowsExceptionWhenNull(): void
    {
        $stimulant = new Stimulant(
            $this->createMock(Group::class),
            $this->createMock(Marker::class),
            'Test Stimulant',
            $this->createVolume()
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Id of Entity App\Domain\Entity\Stimulant is null.');

        $stimulant->getId();
    }
}
