<?php

namespace Unit\Domain\Model\User;

use App\Domain\Model\User\CreateUserModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidModel(): void
    {
        $model = new CreateUserModel(
            1,                  // groupId
            'test@example.com', // email
            'securePass123',    // password
            ['ROLE_USER'],      // roles
            true,               // isActive
            true,               // emailConfirmed
            false,              // phoneConfirmed
            'Europe/Moscow',    // timeZone
            'Иванов',           // lastName
            'Иван',             // firstName
            'Иванович',         // middleName
            'token123',         // refreshToken
            '+79991234567',     // phone
            null,               // avatarLink
            null,              // emailCode (передаем как int, а не string)
            null               // phoneCode (передаем как int, а не string)
        );

        $errors = $this->validator->validate($model);

        // Если тест упадет, этот код выведет в консоль, какие именно поля не прошли
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                echo "\nПоле: " . $error->getPropertyPath() . " | Ошибка: " . $error->getMessage();
            }
        }

        $this->assertCount(0, $errors);
    }

    public function testInvalidEmailAndPhone(): void
    {
        $model = new CreateUserModel(
            1, 'bad-email', '123', [], true, false, false, 'Europe/Moscow',
            'Ivanov', 'Ivan', null, null,
            '8999',             // Невалидный телефон
            null,
            '123',              // Невалидный код (нужно 5 цифр)
            null
        );

        $errors = $this->validator->validate($model);

        $errorPaths = [];
        foreach ($errors as $error) {
            $errorPaths[] = $error->getPropertyPath();
        }

        $this->assertContains('email', $errorPaths);
        $this->assertContains('phone', $errorPaths);
        $this->assertContains('emailCode', $errorPaths);
    }

    public function testLastNameRegex(): void
    {
        $model = new CreateUserModel(
            1, 'test@example.com', 'pass', ['ROLE_USER'], true, false, false, 'Europe/Moscow',
            'Ivanov123',        // Ошибка: цифры запрещены
            'Ivan', null, null, null, null, null, null
        );

        $errors = $this->validator->validate($model);
        $this->assertGreaterThan(0, count($errors));
        $this->assertEquals('lastName', $errors->get(0)->getPropertyPath());
    }

    public function testInvalidTimezone(): void
    {
        $model = new CreateUserModel(
            1, 'test@example.com', 'pass', ['ROLE_USER'], true, false, false,
            'Mars/Base',        // Ошибка: невалидная зона
            'Иванов', 'Иван', null, null, null, null, null, null
        );

        $errors = $this->validator->validate($model);
        $this->assertGreaterThan(0, count($errors));
        $this->assertEquals('timeZone', $errors->get(0)->getPropertyPath());
    }
}
