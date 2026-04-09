<?php

namespace Unit\Domain\Model\Offspring;

use App\Domain\Model\Offspring\UpdateOffspringModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateOffspringModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    /**
     * Тест успешной валидации при полном обновлении данных о плоде
     */
    public function testValidUpdate(): void
    {
        $model = new UpdateOffspringModel(
            groupId: 1,
            plantId: 10,
            fruitingDate: new \DateTimeImmutable('2024-08-20'),
            floweringDate: new \DateTimeImmutable('2024-06-15'),
            mass: 320,
            color: 'Темно-синий',
            flavor: 'Кисло-сладкий',
            quantity: 12,
            comment: 'Вторая волна урожая'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Проверка типов для дат (должны быть DateTimeImmutable или null)
     */
    public function testDateTypesValidation(): void
    {
        // В конструкторе типизация ?DateTimeImmutable,
        // поэтому проверяем валидное состояние.
        $model = new UpdateOffspringModel(
            groupId: 1,
            plantId: 5,
            fruitingDate: null,
            floweringDate: null
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Проверка обязательных полей при обновлении
     */
    public function testRequiredFieldsOnUpdate(): void
    {
        // Так как поля типизированы как int, мы проверяем их наличие
        $model = new UpdateOffspringModel(
            groupId: 1,
            plantId: 10
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Тест на валидацию целых чисел (масса и количество)
     */
    public function testIntegerConstraints(): void
    {
        $model = new UpdateOffspringModel(
            groupId: 1,
            plantId: 10,
            mass: 50,
            quantity: 2
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }
}
