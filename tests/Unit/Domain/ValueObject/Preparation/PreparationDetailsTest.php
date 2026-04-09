<?php

namespace Unit\Domain\ValueObject\Preparation;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Domain\ValueObject\Preparation\PreparationDetails;

class PreparationDetailsTest extends TestCase
{
    public function testInitializationWithValidData(): void
    {
        $manufacturer = 'ООО Зеленый Мир №1 (Россия)';
        $details = new PreparationDetails($manufacturer);

        $this->assertEquals($manufacturer, $details->getManufacturer());
    }

    #[DataProvider('validManufacturerProvider')]
    public function testValidManufacturerCharacters(string $validName): void
    {
        $details = new PreparationDetails(manufacturer: $validName);
        $this->assertEquals($validName, $details->getManufacturer());
    }

    public static function validManufacturerProvider(): array
    {
        return [
            ['Agro-Salyut'],
            ['АгроМастер'],
            ['Plant (USA)'],
            ['Партия №123'],
        ];
    }

    #[DataProvider('invalidManufacturerProvider')]
    public function testManufacturerValidationThrowsException(string $invalidName): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Manufacturer contains invalid characters');

        new PreparationDetails(manufacturer: $invalidName);
    }

    public static function invalidManufacturerProvider(): array
    {
        return [
            ['Manufacturer <script>'],
            ['Brand & Co'],
            ['Dangerous!'],
        ];
    }
}
