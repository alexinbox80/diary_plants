<?php

namespace Unit\Domain\ValueObject\Offspring;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Offspring\Phenology;

class PhenologyTest extends TestCase
{
    public function testInitializationWithDates(): void
    {
        $flowering = new DateTimeImmutable('2023-05-20 10:00:00');
        $fruiting = new DateTimeImmutable('2023-08-15 14:30:00');

        $phenology = new Phenology($fruiting, $flowering);

        $this->assertSame($flowering, $phenology->getFloweringDate());
        $this->assertSame($fruiting, $phenology->getFruitingDate());
    }

    public function testEmptyInitialization(): void
    {
        $phenology = new Phenology();

        $this->assertNull($phenology->getFloweringDate());
        $this->assertNull($phenology->getFruitingDate());
    }

    public function testPartialInitialization(): void
    {
        $flowering = new DateTimeImmutable('now');
        $phenology = new Phenology(floweringDate: $flowering);

        $this->assertSame($flowering, $phenology->getFloweringDate());
        $this->assertNull($phenology->getFruitingDate());
    }

    public function testDatesAreImmutable(): void
    {
        $phenology = new Phenology(new DateTimeImmutable('2023-01-01'));

        $date = $phenology->getFruitingDate();
        $this->assertInstanceOf(DateTimeImmutable::class, $date);

        // Попытка изменить дату создаст новый объект, не меняя состояние внутри Phenology
        $newDate = $date->modify('+1 day');
        $this->assertNotEquals($newDate, $phenology->getFruitingDate());
    }
}
