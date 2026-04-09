<?php

namespace Unit\Domain\ValueObject\User;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\User\Email;

class EmailTest extends TestCase
{
    public function testValidEmailInitialization(): void
    {
        $value = 'User@Example.Com';
        $email = new Email($value);

        // Проверяем автоматическое приведение к нижнему регистру
        $this->assertEquals('user@example.com', $email->toString());
        $this->assertEquals('user@example.com', (string)$email);
    }

    public function testInvalidEmailFormatThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The email must be a valid email address');

        new Email('invalid-email-string');
    }

    public function testTooLongEmailThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The email must be a 255 chars length');

        $longEmail = str_repeat('a', 250) . '@test.com';
        new Email($longEmail);
    }

    public function testIsEqualComparison(): void
    {
        $email1 = new Email('test@example.com');
        $email2 = new Email('TEST@example.com'); // другой регистр
        $email3 = new Email('other@example.com');

        $this->assertTrue($email1->isEqual($email2), 'Emails should be equal regardless of case');
        $this->assertFalse($email1->isEqual($email3));
    }

    public function testEmailWithSpecialCharacters(): void
    {
        $validEmail = 'dev.test+filter@sub.domain.io';
        $email = new Email($validEmail);

        $this->assertEquals($validEmail, $email->toString());
    }
}
