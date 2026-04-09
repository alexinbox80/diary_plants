<?php

namespace Unit\Domain\ValueObject;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Price;
use App\Domain\ValueObject\Enum\Currency;

class PriceTest extends TestCase
{
    public function testInitialization(): void
    {
        $amount = 1000;
        $currency = Currency::RUR;

        $price = new Price($amount, $currency);

        $this->assertEquals($amount, $price->getAmount());
        $this->assertSame($currency, $price->getCurrency());
    }

    public function testAmountMustBePositive(): void
    {
        $this->expectException(InvalidArgumentException::class);

        // Попытка создать цену с нулевым или отрицательным значением
        new Price(0, Currency::USD);
    }

    public function testToStringFormats(): void
    {
        $price = new Price(500, Currency::EUR);

        $this->assertEquals('500 EUR', $price->toString());
        $this->assertEquals('500 EUR', (string)$price);
    }

    public function testIsEqual(): void
    {
        $price1 = new Price(100, Currency::USD);
        $price2 = new Price(100, Currency::USD);
        $price3 = new Price(200, Currency::USD);
        $price4 = new Price(100, Currency::EUR);

        $this->assertTrue($price1->isEqual($price2));
        $this->assertFalse($price1->isEqual($price3));
        $this->assertFalse($price1->isEqual($price4));
    }

    public function testFromString(): void
    {
        $priceStr = '1500 RUR';
        $price = Price::fromString($priceStr);

        $this->assertEquals(1500, $price->getAmount());
        $this->assertSame(Currency::RUR, $price->getCurrency());
    }
}
