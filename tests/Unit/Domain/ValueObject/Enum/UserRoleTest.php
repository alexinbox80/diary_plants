<?php

namespace Unit\Domain\ValueObject\Enum;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Enum\UserRole;


class UserRoleTest extends TestCase
{
    public function testIsValid(): void
    {
        $this->assertTrue(UserRole::isValid('ROLE_ADMIN'));
        $this->assertTrue(UserRole::isValid('ROLE_USER'));
        $this->assertFalse(UserRole::isValid('ROLE_SUPER_ADMIN'));
        $this->assertFalse(UserRole::isValid(''));
    }

    public function testGetLabelReturnsRussianTranslation(): void
    {
        $this->assertEquals('Администратор', UserRole::getLabel('ROLE_ADMIN'));
        $this->assertEquals('Пользователь', UserRole::getLabel('ROLE_USER'));
        $this->assertEquals('Неизвестно', UserRole::getLabel('UNKNOWN'));
    }

    public function testAllRolesToStringReturnsCommaSeparatedList(): void
    {
        $result = UserRole::AllRolesToString();

        $this->assertStringContainsString('ROLE_USER', $result);
        $this->assertStringContainsString('ROLE_ADMIN', $result);
        $this->assertStringContainsString('ROLE_MANAGER', $result);
        $this->assertStringContainsString('ROLE_GUEST', $result);
    }

    public function testToStringConvertsMixedArrayToString(): void
    {
        $roles = [UserRole::ROLE_ADMIN, 'ROLE_USER'];
        $result = UserRole::toString($roles);

        $this->assertEquals('ROLE_ADMIN,ROLE_USER', $result);
    }

    public function testToStringThrowsExceptionOnInvalidType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        UserRole::toString([123]);
    }

    public function testToArrayConvertsStringBackToEnumArray(): void
    {
        $rolesString = 'ROLE_ADMIN, ROLE_USER, INVALID_ROLE';
        $result = UserRole::toArray($rolesString);

        $this->assertCount(2, $result);
        $this->assertSame(UserRole::ROLE_ADMIN, $result[0]);
        $this->assertSame(UserRole::ROLE_USER, $result[1]);
    }

    public function testToArrayReturnsEmptyArrayOnNullOrEmpty(): void
    {
        $this->assertEmpty(UserRole::toArray(null));
        $this->assertEmpty(UserRole::toArray(''));
    }

    public function testGetChoicesForSymfonyForms(): void
    {
        $choices = UserRole::getChoices();

        $this->assertArrayHasKey('Менеджер', $choices);
        $this->assertEquals('ROLE_MANAGER', $choices['Менеджер']);
    }
}
