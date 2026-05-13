<?php

namespace Unit\Domain\Exception;

use App\Domain\Exception\EntityNotFoundException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Exception;

#[CoversClass(EntityNotFoundException::class)]
class EntityNotFoundExceptionTest extends TestCase
{
    #[Test]
    public function testConstructorWithDefaultValues(): void
    {
        // Создаем исключение с параметрами по умолчанию
        $exception = new EntityNotFoundException();

        // Проверяем дефолтные значения
        $this->assertSame('Entity not found', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    #[Test]
    public function testConstructorWithCustomValues(): void
    {
        $message = 'Plant with ID 42 not found';
        $code = 404;
        $previousException = new Exception('Original database connection error');

        // Создаем исключение со всеми кастомными параметрами
        $exception = new EntityNotFoundException($message, $code, $previousException);

        // Проверяем, что все переданные значения корректно пробросились
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
        $this->assertSame($previousException, $exception->getPrevious());
    }
}
