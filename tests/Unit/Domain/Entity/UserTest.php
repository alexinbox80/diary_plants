<?php

namespace Unit\Domain\Entity;

use App\Domain\Entity\User;
use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\User\Name;
use App\Domain\ValueObject\User\Email;
use App\Domain\ValueObject\User\Phone;

class UserTest extends TestCase
{
    private function createGroupMock(): Group
    {
        return $this->createMock(Group::class);
    }

    // Вместо моков создаем реальные объекты
    private function createEmail(): Email
    {
        return new Email('test@example.com');
    }

    private function createPhone(): Phone
    {
        return new Phone('+79991234567');
    }

    private function createName(): Name
    {
        return new Name('Иван', 'Иванов', 'Иванович');
    }

    public function testPhoneHandling(): void
    {
        $user = new User(
            $this->createGroupMock(),
            $this->createEmail(),
            'pass',
            $this->createName()
        );

        $phone = $this->createPhone();

        // Проверяем установку телефона
        $user->setPhone($phone);
        $this->assertSame($phone, $user->getPhone());

        // Проверяем генерацию кода и статус до подтверждения
        $user->generatePhoneCode('654321');
        $this->assertEquals('654321', $user->getPhoneCode());
        $this->assertFalse($user->isPhoneConfirmed());

        // Проверяем подтверждение
        $user->confirmPhone();
        $this->assertTrue($user->isPhoneConfirmed());
        $this->assertNull($user->getPhoneCode(), 'Код должен быть занулен после подтверждения');
    }

    public function testConstructorInitializesCorrectly(): void
    {
        $group = $this->createGroupMock();
        $email = $this->createEmail();
        $name = $this->createName();
        $password = 'hashed_password';
        $roles = ['ROLE_ADMIN'];

        $user = new User($group, $email, $password, $name, $roles);

        $this->assertSame($group, $user->getGroup());
        $this->assertSame($email, $user->getEmail());
        $this->assertEquals($password, $user->getPassword());
        $this->assertSame($name, $user->getName());
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertTrue($user->isActive());
    }

    public function testSecurityMethods(): void
    {
        $user = new User($this->createGroupMock(), $this->createEmail(), 'pass', $this->createName());

        // Проверяем, что getUserIdentifier возвращает строку из объекта Email
        $this->assertEquals('test@example.com', (string)$user->getUserIdentifier());

        $user->upgradePassword('new_hash');
        $this->assertEquals('new_hash', $user->getPassword());
    }

    public function testRoleManagement(): void
    {
        $user = new User($this->createGroupMock(), $this->createEmail(), 'pass', $this->createName());

        $user->addRole('ROLE_MANAGER');
        $this->assertContains('ROLE_MANAGER', $user->getRoles());

        $user->changeRole('ROLE_SUPER_ADMIN');
        $this->assertEquals(['ROLE_SUPER_ADMIN'], $user->getRoles());

        $user->removeRole('ROLE_SUPER_ADMIN');
        $this->assertEmpty($user->getRoles());
    }

    public function testActivationAndConfirmation(): void
    {
        $user = new User($this->createGroupMock(), $this->createEmail(), 'pass', $this->createName());

        $user->suspend();
        $this->assertFalse($user->isActive());
        $user->activate();
        $this->assertTrue($user->isActive());

        $user->generateEmailCode('123456');
        $this->assertEquals('123456', $user->getEmailCode());
        $user->confirmEmail();
        // В сущности confirmEmail должен возвращать true на isEmailConfirmed()
        $this->assertTrue($user->isEmailConfirmed());
        $this->assertNull($user->getEmailCode());
    }

    public function testGetIdThrowsExceptionWhenNull(): void
    {
        $user = new User($this->createGroupMock(), $this->createEmail(), 'pass', $this->createName());

        $this->expectException(\InvalidArgumentException::class);
        $user->getId();
    }
}
