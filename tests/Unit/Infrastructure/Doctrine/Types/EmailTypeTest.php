<?php

namespace Unit\Infrastructure\Doctrine\Types;

use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\User\Email;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use App\Infrastructure\Doctrine\Types\EmailType;

class EmailTypeTest extends TestCase
{
    private EmailType $type;
    private PostgreSQLPlatform $platform;

    protected function setUp(): void
    {
        if (!Type::hasType(EmailType::NAME)) {
            Type::addType(EmailType::NAME, EmailType::class);
        }

        $this->type = Type::getType(EmailType::NAME);
        $this->platform = new PostgreSQLPlatform();
    }

    public function testConvertToDatabaseValueReturnsString(): void
    {
        $emailStr = 'test@example.com';
        $emailVo = new Email($emailStr);

        $result = $this->type->convertToDatabaseValue($emailVo, $this->platform);

        $this->assertEquals($emailStr, $result);
    }

    public function testConvertToDatabaseValueReturnsNullOnNull(): void
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testConvertToPHPValueReturnsEmailObject(): void
    {
        $emailStr = 'user@test.com';

        $result = $this->type->convertToPHPValue($emailStr, $this->platform);

        $this->assertInstanceOf(Email::class, $result);
        $this->assertEquals($emailStr, $result->toString());
    }

    public function testConvertToPHPValueReturnsNullOnNull(): void
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testConvertToPHPValueThrowsExceptionOnEmptyString(): void
    {
        // WebmozartAssert выбросит \InvalidArgumentException
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Email type is represented by a varchar database type');

        $this->type->convertToPHPValue('', $this->platform);
    }
}
