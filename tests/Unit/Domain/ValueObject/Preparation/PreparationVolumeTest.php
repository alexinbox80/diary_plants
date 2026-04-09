<?php

namespace Unit\Domain\ValueObject\Preparation;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Preparation\PreparationVolume;

class PreparationVolumeTest extends TestCase
{
    public function testValidInitialization(): void
    {
        $amount = 10;
        $rate = '5 мл на 10 л воды';

        $volume = new PreparationVolume($amount, $rate);

        $this->assertEquals($amount, $volume->getAmount());
        $this->assertEquals($rate, $volume->getApplicationRate());
    }

    public function testAmountMustBeGreaterThanZero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Amount must be positive');

        new PreparationVolume(0);
    }

    public function testApplicationRateTooShort(): void
    {
        $this->expectException(InvalidArgumentException::class);
        // Ожидаем исключение, так как 1 символ меньше минимальных 2
        new PreparationVolume(10, '1');
    }

    public function testApplicationRateTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $longRate = str_repeat('a', 51);

        new PreparationVolume(10, $longRate);
    }

    public function testNullableApplicationRate(): void
    {
        $volume = new PreparationVolume(100, null);

        $this->assertEquals(100, $volume->getAmount());
        $this->assertNull($volume->getApplicationRate());
    }
}
