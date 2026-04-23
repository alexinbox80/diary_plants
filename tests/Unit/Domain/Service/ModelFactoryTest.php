<?php

namespace Unit\Domain\Service;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\ModelFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[CoversClass(ModelFactory::class)]
class ModelFactoryTest extends TestCase
{
    private ValidatorInterface|MockObject $validator;
    private ModelFactory $factory;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->factory = new ModelFactory($this->validator);
    }

    #[Test]
    public function testMakeModelSuccess(): void
    {
        // Используем тестовый класс (можно вынести в отдельный файл или использовать существующее DTO)
        $modelClass = TestDto::class;
        $params = [1, 'test'];

        // Ожидаем пустой список нарушений
        $violations = $this->createMock(ConstraintViolationListInterface::class);
        $violations->method('count')->willReturn(0);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($this->isInstanceOf($modelClass))
            ->willReturn($violations);

        $result = $this->factory->makeModel($modelClass, ...$params);

        $this->assertInstanceOf($modelClass, $result);
        $this->assertSame(1, $result->id);
        $this->assertSame('test', $result->name);
    }

    #[Test]
    public function testMakeModelThrowsValidationException(): void
    {
        $modelClass = TestDto::class;

        // Имитируем наличие ошибок валидации
        $violations = new ConstraintViolationList([
            $this->createMock(ConstraintViolationInterface::class)
        ]);

        $this->validator->method('validate')->willReturn($violations);

        $this->expectException(ValidationFailedException::class);

        $this->factory->makeModel($modelClass, 1, 'invalid');
    }
}

/**
 * Простой вспомогательный класс для теста
 */
class TestDto {
    public function __construct(public int $id, public string $name) {}
}
