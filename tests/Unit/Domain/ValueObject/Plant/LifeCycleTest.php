<?php

namespace Unit\Domain\ValueObject\Plant;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Plant\LifeCycle;

class LifeCycleTest extends TestCase
{
    public function testInitializationWithFullData(): void
    {
        $planting = new DateTimeImmutable('2023-03-01');
        $vaccination = new DateTimeImmutable('2023-06-15');
        $soil = 'Торф, перлит, биогумус (5:1:2)';

        $lifeCycle = new LifeCycle($planting, $vaccination, $soil);

        $this->assertSame($planting, $lifeCycle->getPlantingDate());
        $this->assertSame($vaccination, $lifeCycle->getVaccinationDate());
        $this->assertEquals($soil, $lifeCycle->getSoil());
    }

    public function testEmptyInitialization(): void
    {
        $lifeCycle = new LifeCycle();

        $this->assertNull($lifeCycle->getPlantingDate());
        $this->assertNull($lifeCycle->getVaccinationDate());
        $this->assertNull($lifeCycle->getSoil());
    }

    public function testPartialInitializationWithSoilOnly(): void
    {
        $soilDescription = 'Универсальный грунт';
        $lifeCycle = new LifeCycle(soil: $soilDescription);

        $this->assertEquals($soilDescription, $lifeCycle->getSoil());
        $this->assertNull($lifeCycle->getPlantingDate());
        $this->assertNull($lifeCycle->getVaccinationDate());
    }

    public function testGettersReturnCorrectTypes(): void
    {
        $lifeCycle = new LifeCycle(new DateTimeImmutable());

        $this->assertInstanceOf(DateTimeImmutable::class, $lifeCycle->getPlantingDate());
        $this->assertIsString($lifeCycle->getSoil() ?? ''); // если не null, то строка
    }
}
