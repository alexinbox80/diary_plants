<?php

namespace Unit\Domain\ValueObject\Plant;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Price;
use App\Domain\ValueObject\Enum\Currency;
use App\Domain\ValueObject\Plant\SalesInfo;

class SalesInfoTest extends TestCase
{
    private function createPriceStub(float $amount): Price
    {
        return new Price($amount, Currency::from('RUR'));
    }

    public function testInitializationWithFullData(): void
    {
        $date = new DateTimeImmutable('2023-12-01');
        $price = $this->createPriceStub(100.0);

        $salesInfo = new SalesInfo($date, $price, true);

        $this->assertSame($date, $salesInfo->getSellingDate());
        $this->assertSame($price, $salesInfo->getSellingPrice());
        $this->assertTrue($salesInfo->isSold());
    }

    public function testDefaultValues(): void
    {
        $salesInfo = new SalesInfo();

        $this->assertNull($salesInfo->getSellingDate());
        $this->assertNull($salesInfo->getSellingPrice());
        $this->assertFalse($salesInfo->isSold(), 'By default isSold should be false');
    }

    public function testPartialInitialization(): void
    {
        $price = $this->createPriceStub(120.0);
        // Указываем, что продано, но дату не ставим
        $salesInfo = new SalesInfo(sellingPrice: $price, isSold: true);

        $this->assertSame($price, $salesInfo->getSellingPrice());
        $this->assertTrue($salesInfo->isSold());
        $this->assertNull($salesInfo->getSellingDate());
    }
}
