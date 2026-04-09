<?php

namespace Unit\Domain\ValueObject\Plant;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Price;
use App\Domain\ValueObject\Enum\Currency;
use App\Domain\ValueObject\Plant\PurchaseInfo;

class PurchaseInfoTest extends TestCase
{
    private function createPriceStub(float $amount): Price
    {
        return new Price($amount, Currency::from('RUR'));
    }

    public function testInitializationWithFullData(): void
    {
        $price = $this->createPriceStub(1000.0);
        $shipping = $this->createPriceStub(300.0);
        $packaging = $this->createPriceStub(50.0);
        $date = new DateTimeImmutable('2023-10-27');
        $seller = 'Иван Иванов';
        $nursery = 'Зеленый Сад';

        $info = new PurchaseInfo($price, $shipping, $packaging, $seller, $nursery, $date);

        $this->assertSame($price, $info->getPrice());
        $this->assertSame($shipping, $info->getShippingCost());
        $this->assertSame($packaging, $info->getPackagingCost());
        $this->assertEquals($seller, $info->getSeller());
        $this->assertEquals($nursery, $info->getNursery());
        $this->assertSame($date, $info->getPurchaseDate());
    }

    public function testEmptyInitialization(): void
    {
        $info = new PurchaseInfo();

        $this->assertNull($info->getPrice());
        $this->assertNull($info->getShippingCost());
        $this->assertNull($info->getPackagingCost());
        $this->assertNull($info->getSeller());
        $this->assertNull($info->getNursery());
        $this->assertNull($info->getPurchaseDate());
    }

    public function testPartialInitialization(): void
    {
        $price = $this->createPriceStub(500.0);
        $info = new PurchaseInfo(price: $price, seller: 'Wildberries');

        $this->assertSame($price, $info->getPrice());
        $this->assertEquals('Wildberries', $info->getSeller());
        $this->assertNull($info->getNursery());
    }
}
