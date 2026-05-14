<?php

namespace User\Application\Security\Voter;

use App\Domain\Entity\User;
use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\ValueObject\Enum\UserRole;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Entity\Interfaces\GroupOwnedInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[CoversClass(GroupOwnershipVoter::class)]
class GroupOwnershipVoterTest extends TestCase
{
    private TokenInterface|MockObject $token;
    private GroupOwnedInterface|MockObject $subject;
    private GroupOwnershipVoter $voter;

    protected function setUp(): void
    {
        $this->token = $this->createMock(TokenInterface::class);
        $this->subject = $this->createMock(GroupOwnedInterface::class);
        $this->voter = new GroupOwnershipVoter();
    }

    #[Test]
    public function testAbstainWhenAttributeIsNotSupported(): void
    {
        // Неподдерживаемый атрибут
        $result = $this->voter->vote($this->token, $this->subject, ['UNSUPPORTED_ATTRIBUTE']);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    #[Test]
    public function testAbstainWhenSubjectIsNotSupported(): void
    {
        // Неподдерживаемый субъект (не реализует GroupOwnedInterface)
        $wrongSubject = new \stdClass();

        $result = $this->voter->vote($this->token, $wrongSubject, [GroupOwnershipVoter::VIEW]);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    #[Test]
    public function testDeniedWhenUserIsNotAuthenticated(): void
    {
        // Пользователь не авторизован (getUser() возвращает null или анонима)
        $this->token->method('getUser')->willReturn(null);

        $result = $this->voter->vote($this->token, $this->subject, [GroupOwnershipVoter::EDIT]);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    #[Test]
    public function testGrantedWhenUserIsAdmin(): void
    {
        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn([UserRole::ROLE_ADMIN->value]);
        $this->token->method('getUser')->willReturn($user);

        // Для админа доступ разрешен всегда, независимо от ID группы субъекта
        $result = $this->voter->vote($this->token, $this->subject, [GroupOwnershipVoter::DELETE]);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    #[Test]
    public function testGrantedWhenUserOwnsTheGroup(): void
    {
        $group = $this->createMock(Group::class);
        $group->method('getId')->willReturn(10);

        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn([UserRole::ROLE_MANAGER->value]);
        $user->method('getGroup')->willReturn($group);

        $this->token->method('getUser')->willReturn($user);

        // ID группы пользователя совпадает с ID группы объекта
        $this->subject->method('getGroupId')->willReturn(10);

        $result = $this->voter->vote($this->token, $this->subject, [GroupOwnershipVoter::VIEW]);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    #[Test]
    public function testDeniedWhenUserDoesNotOwnTheGroup(): void
    {
        $group = $this->createMock(Group::class);
        $group->method('getId')->willReturn(10);

        $user = $this->createMock(User::class);
        $user->method('getRoles')->willReturn([UserRole::ROLE_MANAGER->value]);
        $user->method('getGroup')->willReturn($group);

        $this->token->method('getUser')->willReturn($user);

        // ID групп не совпадают (у пользователя 10, у объекта 20)
        $this->subject->method('getGroupId')->willReturn(20);

        $result = $this->voter->vote($this->token, $this->subject, [GroupOwnershipVoter::EDIT]);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }
}
