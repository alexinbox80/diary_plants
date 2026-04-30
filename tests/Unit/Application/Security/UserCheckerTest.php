<?php

namespace Unit\Application\Security;

use App\Domain\Entity\User;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Application\Security\UserChecker;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

#[CoversClass(UserChecker::class)]
class UserCheckerTest extends TestCase
{
    private UserChecker $userChecker;

    protected function setUp(): void
    {
        $this->userChecker = new UserChecker();
    }

    #[Test]
    public function testCheckPreAuthSuccess(): void
    {
        $user = $this->createMock(User::class);
        $user->method('isActive')->willReturn(true);
        $user->method('getDeletedAt')->willReturn(null);

        // Ожидаем, что метод просто выполнится без исключений
        $this->userChecker->checkPreAuth($user);
        $this->assertTrue(true);
    }

    #[Test]
    public function testCheckPreAuthThrowsExceptionWhenInactive(): void
    {
        $user = $this->createMock(User::class);
        $user->method('isActive')->willReturn(false);

        $this->expectException(CustomUserMessageAccountStatusException::class);
        $this->expectExceptionMessage('Ваш аккаунт заблокирован.');

        $this->userChecker->checkPreAuth($user);
    }

    #[Test]
    public function testCheckPreAuthThrowsExceptionWhenDeleted(): void
    {
        $user = $this->createMock(User::class);
        $user->method('isActive')->willReturn(true);
        $user->method('getDeletedAt')->willReturn(new \DateTimeImmutable());

        $this->expectException(CustomUserMessageAccountStatusException::class);
        $this->expectExceptionMessage('Аккаунт удален.');

        $this->userChecker->checkPreAuth($user);
    }

    #[Test]
    public function testCheckPreAuthIgnoresOtherUserInterfaces(): void
    {
        // Например, ваш AuthUser, который не наследует App\Domain\Entity\User
        $user = $this->createMock(UserInterface::class);

        $this->userChecker->checkPreAuth($user);
        $this->assertTrue(true); // Никаких проверок и исключений
    }

    #[Test]
    public function testCheckPostAuthDoesNothing(): void
    {
        $user = $this->createMock(User::class);
        $this->userChecker->checkPostAuth($user);
        $this->assertTrue(true);
    }
}
