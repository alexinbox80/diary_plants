<?php

namespace App\Domain\ValueObject;

use App\Domain\ValueObject\Enum\Currency;
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
        $parts = explode(' ', trim($value));

        // Проверяем, что есть и число, и валюта
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException(sprintf('Wrong price format: "%s". Expected "100 RUR"', $value));
        }

        [$amount, $currencyStr] = $parts;

        $currency = Currency::tryFrom($currencyStr);

        if (null === $currency) {
            throw new \InvalidArgumentException(sprintf('Unknown currency: "%s"', $currencyStr));
        }

        return new self((int) $amount, $currency);
    }
}
