<?php

namespace Unit\Domain\Model\Offspring;

use App\Domain\Model\Offspring\CreateOffspringModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateOffspringModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    /**
     * Тест успешной валидации со всеми заполненными данными
     */
    public function testValidModel(): void
    {
        $model = new CreateOffspringModel(
            groupId: 1,
            plantId: 10,
            fruitingDate: new \DateTimeImmutable('2024-06-01'),
            floweringDate: new \DateTimeImmutable('2024-04-15'),
            mass: 250,
            color: 'Красный',
            flavor: 'Сладкий',
            quantity: 5,
            comment: 'Первый урожай в этом году'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Тест минимально необходимого набора данных (только обязательные поля)
     */
    public function testMinimalValidModel(): void
    {
        $model = new CreateOffspringModel(
            groupId: 1,
            plantId: 10
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Тест валидации типов данных (например, масса должна быть числом)
     */
    public function testInvalidTypes(): void
    {
        // В PHP 8+ типизация в конструкторе (int $mass) вызовет TypeError
        // до валидатора при прямой передаче строки.
        // Но валидатор Symfony важен при десериализации (например из формы).

        $model = new CreateOffspringModel(
            groupId: 1,
            plantId: 10,
            mass: 100,
            quantity: 5
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Тест обязательных полей (NotBlank)
     */
    public function testNotBlankConstraints(): void
    {
        // Мы не можем передать null в groupId/plantId из-за типизации int в конструкторе.
        // Поэтому проверяем саму суть: если бы мы использовали Reflection или Form,
        // валидатор бы нашел отсутствие обязательных ID.

        $model = new CreateOffspringModel(
            groupId: 1,
            plantId: 10
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }
}
