<?php

namespace User\Domain\Exception;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Exception\UserNotFoundException;

#[CoversClass(UserNotFoundException::class)]
class UserNotFoundExceptionTest extends TestCase
{
    #[Test]
    public function testConstructorFormatsMessageCorrectly(): void
    {
        $userId = 42;
        $expectedMessage = 'Пользователь с ID "42" не найден.';

        // Инициализируем исключение с тестовым ID
        $exception = new UserNotFoundException($userId);

        // Проверяем, что sprintf корректно подставил ID в шаблон строки
        $this->assertSame($expectedMessage, $exception->getMessage());
    }

    #[Test]
    public function testConstructorWorksWithDifferentIds(): void
    {
        $userId = 999123;
        $expectedMessage = 'Пользователь с ID "999123" не найден.';

        $exception = new UserNotFoundException($userId);

        $this->assertSame($expectedMessage, $exception->getMessage());
    }
}
