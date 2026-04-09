<?php

namespace Unit\Domain\Model\User;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\User;
use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use App\Domain\Model\User\UserModel;
use App\Domain\ValueObject\User\Name;
use App\Domain\Model\Group\GroupModel;
use App\Domain\ValueObject\User\Email;
use App\Domain\ValueObject\User\Phone;

class UserModelTest extends TestCase
{
    public function testFromEntityAndToArray(): void
    {
        $tz = new DateTimeZone('Europe/Moscow');
        $now = new DateTimeImmutable('2024-01-01 10:00:00', $tz);

        // 1. Создаем реальные Value Objects (замените на свои конструкторы, если они отличаются)
        $emailObject = new Email('test@example.com');

        $phoneObject = new Phone('+79990000000');

        $name = new Name(
            'Иванов',
            'Иван',
            'Иванович'
        );

        // 2. Мокаем сущность User
        $groupEntity = $this->createMock(Group::class);
        $groupEntity->method('getId')->willReturn(1);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(100);
        $user->method('getGroup')->willReturn($groupEntity);
        $user->method('getEmail')->willReturn($emailObject);
        $user->method('getPassword')->willReturn('hashed_password');
        $user->method('getRoles')->willReturn(['ROLE_USER']);
        $user->method('isActive')->willReturn(true);
        $user->method('isEmailConfirmed')->willReturn(true);
        $user->method('isPhoneConfirmed')->willReturn(false);
        $user->method('getTimeZone')->willReturn('Europe/Moscow');
        $user->method('getName')->willReturn($name);
        $user->method('getRefreshToken')->willReturn('token123');
        $user->method('getPhone')->willReturn($phoneObject);
        $user->method('getAvatarLink')->willReturn('https://avatar.com');
        $user->method('getEmailCode')->willReturn('1234');
        $user->method('getPhoneCode')->willReturn(null);
        $user->method('getCreatedAt')->willReturn($now);
        $user->method('getUpdatedAt')->willReturn($now);

        $groupModel = $this->createMock(GroupModel::class);
        $groupModel->method('getTitle')->willReturn('Администраторы');

        // 3. Вызов метода
        $model = UserModel::fromEntity($user, $groupModel);

        // 4. Проверки
        $this->assertEquals(100, $model->getId());
        $this->assertEquals('test@example.com', $model->getEmail());

        $array = $model->toArray();
        $this->assertEquals('Иванов', $array['last_name']);
        $this->assertEquals('Да', $array['is_active']);
        $this->assertEquals('01.01.2024 10:00:00', $array['created_at']);
    }

    public function testGetTableHeaderRu(): void
    {
        $headers = UserModel::getTableHeaderRu();
        $this->assertEquals('Электронная почта', $headers['email']);
        $this->assertEquals('Фамилия', $headers['last_name']);
    }
}
