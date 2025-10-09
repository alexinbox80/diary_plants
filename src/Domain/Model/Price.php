<?php

namespace App\Domain\Model;

use Webmozart\Assert\Assert;

/**
 * Value Object моделирующий цену
 */
final class Price
{
    private int $amount;

    private Currency $currency;

    public function __construct(int $amount, Currency $currency)
    {
        Assert::greaterThan($amount, 0);

        $this->amount = $amount;
        $this->currency = $currency;
    }

    public function __toString(): string
    {
        return sprintf('%s %s', $this->amount, $this->currency->value);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function isEqual(self $other): bool
    {
        return $this->amount === $other->amount
            && $this->currency === $other->currency;
    }

    public function toString(): ?string
    {
        return $this->amount != null ? $this->amount . ' ' . $this->currency->value : null;
    }

    public static function fromString(string $value): self
    {
        [$amount, $currency] = explode(' ', $value);

        return new self((int) $amount, Currency::tryFrom($currency));
    }
}
