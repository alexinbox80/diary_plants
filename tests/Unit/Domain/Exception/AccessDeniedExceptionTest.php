<?php

namespace Unit\Domain\Exception;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Exception\AccessDeniedException;

#[CoversClass(AccessDeniedException::class)]
class AccessDeniedExceptionTest extends TestCase
{
    #[Test]
    public function testConstructorWithDefaultMessage(): void
    {
        // Вызываем конструктор без аргументов
        $exception = new AccessDeniedException();

        // Проверяем, что применилось дефолтное сообщение
        $this->assertSame('Доступ к данному ресурсу запрещен.', $exception->getMessage());
    }

    #[Test]
    public function testConstructorWithCustomMessage(): void
    {
        $customReason = 'У вас нет прав для редактирования этой группы.';

        // Вызываем конструктор с кастомным текстом
        $exception = new AccessDeniedException($customReason);

        $this->assertSame($customReason, $exception->getMessage());
    }

    #[Test]
    public function testForUserRoleFactoryMethod(): void
    {
        $role = 'ROLE_ADMIN';
        $expectedMessage = 'Требуется роль ROLE_ADMIN для выполнения этого действия.';

        // Вызываем статический фабричный метод
        $exception = AccessDeniedException::forUserRole($role);

        // Проверяем корректность сборки объекта и форматирования строки
        $this->assertInstanceOf(AccessDeniedException::class, $exception);
        $this->assertSame($expectedMessage, $exception->getMessage());
    }
}
