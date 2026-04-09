<?php

namespace Unit\Domain\Model\User;

use App\Domain\Model\User\UpdateUserModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateUserModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidUpdate(): void
    {
        // Передаем все аргументы позиционно, чтобы не было ошибок количества аргументов
        $model = new UpdateUserModel(
            1,                  // groupId
            'update@test.com',  // email
            null,               // password (опционален в update)
            ['ROLE_USER'],      // roles
            true,               // isActive
            true,               // emailConfirmed
            false,              // phoneConfirmed
            'Europe/Moscow',    // timeZone
            'Петров',           // lastName
            'Петр',              // firstName
            'Петрович',         // middleName
            'token_new',        // refreshToken
            '+79001112233',     // phone
            null,               // avatarLink
            null,               // emailCode (null во избежание конфликта Type/Length)
            null                // phoneCode
        );

        $errors = $this->validator->validate($model);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                fwrite(STDERR, sprintf("\nПоле: %s | Ошибка: %s", $error->getPropertyPath(), $error->getMessage()));
            }
        }

        $this->assertCount(0, $errors);
    }

    public function testInvalidEmailFormat(): void
    {
        $model = new UpdateUserModel(
            1, 'not-an-email', null, [], true, false, false, 'Europe/Moscow',
            'Petrov', 'Petr', null, null, null, null, null, null
        );

        $errors = $this->validator->validate($model);
        $this->assertGreaterThan(0, count($errors));

        $errorFields = array_map(fn($e) => $e->getPropertyPath(), iterator_to_array($errors));
        $this->assertContains('email', $errorFields);
    }

    public function testLastNameLettersOnly(): void
    {
        $model = new UpdateUserModel(
            1, 'test@test.com', null, [], true, false, false, 'Europe/Moscow',
            'Petrov123', // Ошибка Regex
            'Petr', null, null, null, null, null, null
        );

        $errors = $this->validator->validate($model);
        $this->assertGreaterThan(0, count($errors));
        $this->assertEquals('lastName', $errors->get(0)->getPropertyPath());
    }
}
