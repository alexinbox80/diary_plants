<?php

namespace Unit\Domain\ValueObject\User;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\ValueObject\User\RefreshToken;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RefreshToken::class)]
class RefreshTokenTest extends TestCase
{
    #[Test]
    public function testSuccessCreation(): void
    {
        $tokenStr = bin2hex(random_bytes(16)); // 32 символа
        $expiresAt = new DateTimeImmutable('+1 day');

        $refreshToken = new RefreshToken($tokenStr, $expiresAt);

        $this->assertSame($tokenStr, $refreshToken->getToken());
        $this->assertSame($expiresAt, $refreshToken->getExpiresAt());
    }

    #[Test]
    public function testThrowsExceptionIfTokenIsTooShort(): void
    {
        $shortToken = 'short-token'; // меньше 20 символов
        $expiresAt = new DateTimeImmutable('+1 day');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Token is too short');

        new RefreshToken($shortToken, $expiresAt);
    }

    #[Test]
    public function testIsExpiredReturnsTrueForPastDate(): void
    {
        $expiresAt = new DateTimeImmutable('-1 minute');
        $refreshToken = new RefreshToken(bin2hex(random_bytes(16)), $expiresAt);

        $this->assertTrue($refreshToken->isExpired());
    }

    #[Test]
    public function testIsExpiredReturnsFalseForFutureDate(): void
    {
        $expiresAt = new DateTimeImmutable('+1 hour');
        $refreshToken = new RefreshToken(bin2hex(random_bytes(16)), $expiresAt);

        $this->assertFalse($refreshToken->isExpired());
    }

    #[Test]
    public function testGenerateCreatesValidToken(): void
    {
        $refreshToken = RefreshToken::generate();

        $this->assertNotNull($refreshToken->getToken());
        $this->assertEquals(32, strlen($refreshToken->getToken()));
        $this->assertFalse($refreshToken->isExpired());

        // Проверяем, что дата истечения примерно через 30 дней
        $expectedDate = new DateTimeImmutable('+30 days');
        $this->assertEqualsWithDelta(
            $expectedDate->getTimestamp(),
            $refreshToken->getExpiresAt()->getTimestamp(),
            2 // дельта в 2 секунды на выполнение теста
        );
    }

    #[Test]
    public function testNullTokenAllowed(): void
    {
        $refreshToken = new RefreshToken(null, null);

        $this->assertNull($refreshToken->getToken());
        $this->assertNull($refreshToken->getExpiresAt());
    }
}
