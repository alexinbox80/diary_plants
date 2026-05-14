<?php

namespace Unit\Application\Security;

use LogicException;
use App\Domain\Entity\User;
use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\ValueObject\Enum\UserRole;
use App\Application\Security\AccessContext;
use Symfony\Bundle\SecurityBundle\Security;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Contracts\Translation\TranslatorInterface;

#[CoversClass(AccessContext::class)]
class AccessContextTest extends TestCase
{
    private Security|MockObject $security;
    private TranslatorInterface|MockObject $translator;
    private AccessContext $accessContext;

    protected function setUp(): void
    {
        $this->security = $this->createMock(Security::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->accessContext = new AccessContext($this->security, $this->translator);
    }

    #[Test]
    public function testGetTargetGroupIdReturnsNullForAdmin(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn([UserRole::ROLE_ADMIN->value]);

        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->assertNull($this->accessContext->getTargetGroupId());
    }

    #[Test]
    public function testGetTargetGroupIdReturnsIdForNonAdminUser(): void
    {
        $group = $this->createMock(Group::class);
        $group->method('getId')->willReturn(42);

        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn([UserRole::ROLE_MANAGER->value]);
        $user->method('getGroup')->willReturn($group);

        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->assertSame(42, $this->accessContext->getTargetGroupId());
    }

    #[Test]
    public function testGetUserReturnsUserWhenAuthenticated(): void
    {
        $user = $this->createMock(User::class);

        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $result = $this->accessContext->getUser();

        $this->assertSame($user, $result);
    }

    #[Test]
    public function testGetUserThrowsExceptionWhenNotAuthenticated(): void
    {
        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->translator->expects($this->once())
            ->method('trans')
            ->with('security.access_denied.not_auth')
            ->willReturn('Доступ запрещен: пользователь не авторизован.');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Доступ запрещен: пользователь не авторизован.');

        $this->accessContext->getUser();
    }

    #[Test]
    public function testGetTimezoneReturnsUserTimezone(): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('getTimeZone')
            ->willReturn('Europe/Moscow');

        $this->security->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->assertSame('Europe/Moscow', $this->accessContext->getTimezone());
    }
}
