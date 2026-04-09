<?php

namespace Unit\Domain\ValueObject\Watering;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Enum\Watering\WaterType;
use App\Domain\ValueObject\Watering\WateringDetails;
use App\Domain\ValueObject\Enum\Watering\WateringMethod;

class WateringDetailsTest extends TestCase
{
    public function testInitializationWithFullData(): void
    {
        $amount = 500;
        $type = WaterType::SETTLED;
        $method = WateringMethod::TOP; // Предположим, IMMERSION есть в WateringMethod
        $temp = '22.5';

        $details = new WateringDetails($amount, $type, $method, $temp);

        $this->assertEquals($amount, $details->getAmount());
        $this->assertSame($type, $details->getType());
        $this->assertSame($method, $details->getMethod());
        $this->assertEquals(22.5, $details->getTemperature());
        $this->assertIsFloat($details->getTemperature());
    }

    public function testDefaultValues(): void
    {
        // Проверяем инициализацию только с обязательным количеством
        $details = new WateringDetails(250);

        $this->assertEquals(250, $details->getAmount());
        $this->assertSame(WaterType::FILTERED, $details->getType());
        $this->assertSame(WateringMethod::TOP, $details->getMethod());
        $this->assertNull($details->getTemperature());
    }

    public function testTemperatureConversion(): void
    {
        // Проверяем, что строковый decimal из БД корректно отдается как float
        $details = new WateringDetails(100, temperature: '25.0');

        $this->assertSame(25.0, $details->getTemperature());

        $detailsNoTemp = new WateringDetails(100, temperature: null);
        $this->assertNull($detailsNoTemp->getTemperature());
    }

    public function testPartialInitialization(): void
    {
        $details = new WateringDetails(
            amount: 1000,
            type: WaterType::OSMOSIS,
            temperature: '20'
        );

        $this->assertEquals(1000, $details->getAmount());
        $this->assertSame(WaterType::OSMOSIS, $details->getType());
        $this->assertSame(WateringMethod::TOP, $details->getMethod()); // по умолчанию
        $this->assertEquals(20.0, $details->getTemperature());
    }
}
