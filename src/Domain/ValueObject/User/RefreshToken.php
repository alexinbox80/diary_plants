<?php

namespace App\Domain\ValueObject\User;

use DateTimeImmutable;
use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class RefreshToken
{
    //рефреш токен
    #[ORM\Column(name: 'refresh_token', type: 'string', length: 32, nullable: true)]
    private ?string $token = null;

    //Дата и время истечения рефреш токена
    #[ORM\Column(name: 'refresh_token_expires_at', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $expiresAt = null;

    public function __construct(?string $token, ?DateTimeImmutable $expiresAt)
    {
        Assert::length($token, 32, 'Refresh token must be exactly 32 characters.');

        $this->token = $token;
        $this->expiresAt = $expiresAt;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    /**
     * Проверка, действителен ли токен
     */
    public function isExpired(): bool
    {
        return $this->expiresAt < new DateTimeImmutable();
    }

    /**
     * Создание нового токена (например, на 30 дней)
     */
    public static function generate(int $length = 32): self
    {
        $token = bin2hex(random_bytes($length / 2));
        $expiresAt = new DateTimeImmutable('+30 days');

        return new self($token, $expiresAt);
    }
}
