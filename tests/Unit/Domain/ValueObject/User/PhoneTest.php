<?php

namespace Unit\Domain\ValueObject\User;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\User\Phone;

class PhoneTest extends TestCase
{
    public function testValidPhoneInitialization(): void
    {
        $phones = [
            '+79991234567',   // 12 символов
            '89991234567',    // 11 символов
            '9991234567',     // 10 символов
            '7-999-123-45-67' // 15 символов
        ];

        foreach ($phones as $value) {
            $phone = new Phone($value);
            $this->assertEquals($value, $phone->toString());
        }
    }

    public function testTooFewDigitsThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The phone must contain 10-11 digits');

        new Phone('12345'); // Всего 5 цифр
    }

    public function testTooManyDigitsThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The phone must contain 10-11 digits');

        new Phone('7999123456789'); // 13 цифр
    }

    public function testNoDigitsThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The phone must contain digits');

        new Phone('abc-def-gh');
    }

    public function testMaxLengthExceeded(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The phone must be a 16 chars length');

        new Phone('+7 (999) 123-45-67-89'); // Слишком длинная строка форматирования
    }

    public function testIsEqual(): void
    {
        $phone1 = new Phone('89991234567');
        $phone2 = new Phone('89991234567');
        $phone3 = new Phone('+79991234567');

        $this->assertTrue($phone1->isEqual($phone2));
        $this->assertFalse($phone1->isEqual($phone3), 'Phones with different formatting should not be equal in current implementation');
    }
}
