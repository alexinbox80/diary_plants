<?php

namespace App\Domain\ValueObject\User;

use Webmozart\Assert\Assert as WebmozartAssert;

final readonly class Phone
{
    private string $value;

    public function __construct(string $value)
    {
        $this->phoneValidate($value);
        $this->value = $value;
    }

    private function phoneValidate(?string $phone = null): void
    {
        if (!is_null($phone)) {
            WebmozartAssert::maxLength($phone, 16, 'The phone must be a 16 chars length. Got: %s');
            $digitsOnly = preg_replace('/[^0-9]/', '', $phone);
            WebmozartAssert::notEmpty($digitsOnly, 'The phone must contain digits. Got: %s');
            WebmozartAssert::regex($digitsOnly, '/^[0-9]{10,11}$/', 'The phone must contain 10-11 digits. Got: %s');
        }
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function isEqual(self $email): bool
    {
        return $email->toString() === $this->toString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
