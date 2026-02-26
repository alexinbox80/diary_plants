<?php

namespace App\Domain\ValueObject;

use Webmozart\Assert\Assert as WebmozartAssert;

final readonly class Email
{
    private string $value;

    public function __construct(string $value)
    {
        $this->emailValidate($value);
        $this->value = mb_strtolower($value);
    }

    private function emailValidate(?string $email = null): void
    {
        if (!is_null($email)) {
            WebmozartAssert::maxLength($email, 255, 'The email must be a 255 chars length. Got: %s');
            WebmozartAssert::email($email, 'The email must be a valid email address. Got: %s');
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
