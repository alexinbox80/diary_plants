<?php

namespace Unit\Application\Security;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Application\Security\AuthUser;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AuthUser::class)]
class AuthUserTest extends TestCase
{
    #[Test]
    public function testSuccessCreation(): void
    {
        $credentials = [
            'username' => 'test@example.com',
            'roles' => ['ROLE_ADMIN']
        ];

        $user = new AuthUser($credentials);

        $this->assertEquals('test@example.com', $user->getUserIdentifier());
        // Должно быть ROLE_ADMIN + ROLE_USER
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertCount(2, $user->getRoles());
    }

    #[Test]
    public function testRoleUserIsAlwaysPresent(): void
    {
        $credentials = ['username' => 'user'];
        $user = new AuthUser($credentials);

        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    #[Test]
    public function testRolesAreUnique(): void
    {
        $credentials = [
            'username' => 'user',
            'roles' => ['ROLE_USER', 'ROLE_USER'] // дубли
        ];

        $user = new AuthUser($credentials);

        $this->assertCount(1, $user->getRoles());
        $this->assertEquals(['ROLE_USER'], array_values($user->getRoles()));
    }

    #[Test]
    public function testEmptyMethods(): void
    {
        $user = new AuthUser(['username' => 'user']);

        $this->assertSame('', $user->getPassword());
        $user->eraseCredentials(); // Метод ничего не делает, просто проверяем вызов
    }
}
