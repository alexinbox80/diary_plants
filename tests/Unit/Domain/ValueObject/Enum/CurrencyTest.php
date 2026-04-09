<?php

namespace Unit\Domain\ValueObject\Enum;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Enum\Currency;

class CurrencyTest extends TestCase
{
    public function testEnumValuesMatchCases(): void
    {
        $this->assertEquals('USD', Currency::USD->value);
        $this->assertEquals('EUR', Currency::EUR->value);
        $this->assertEquals('RUR', Currency::RUR->value);
    }

    public function testEnumHasAllExpectedCurrencies(): void
    {
        $cases = Currency::cases();

        $this->assertCount(3, $cases);

        $values = array_map(fn($c) => $c->value, $cases);
        $this->assertContains('USD', $values);
        $this->assertContains('EUR', $values);
        $this->assertContains('RUR', $values);
    }

    public function testTryFromReturnsCorrectCase(): void
    {
        $this->assertSame(Currency::USD, Currency::tryFrom('USD'));
        $this->assertNull(Currency::tryFrom('GBP'));
    }
}
