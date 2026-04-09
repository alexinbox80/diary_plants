<?php

namespace Unit\Domain\Model\Marker;

use App\Domain\Model\Marker\UpdateMarkerModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateMarkerModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    /**
     * Тест успешной валидации при обновлении со всеми данными
     */
    public function testValidUpdate(): void
    {
        $model = new UpdateMarkerModel(
            groupId: 5,
            letter: 'H2O',
            color: '#3357FF',
            type: 'watering',
            description: 'Regular schedule',
            colorDescription: 'Blue'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Проверка ограничения длины обозначения (max 3)
     */
    public function testInvalidLetterLengthOnUpdate(): void
    {
        $model = new UpdateMarkerModel(
            groupId: 1,
            letter: 'TEST', // Ошибка: максимум 3 символа
            color: '#000000',
            type: 'pest'
        );

        $errors = $this->validator->validate($model);
        $this->assertGreaterThan(0, $errors);
        $this->assertEquals('letter', $errors[0]->getPropertyPath());
    }

    /**
     * Проверка строгого соответствия длины цвета (7 символов)
     */
    public function testInvalidColorFormatOnUpdate(): void
    {
        $model = new UpdateMarkerModel(
            groupId: 1,
            letter: 'F',
            color: '#FF00', // Ошибка: должно быть ровно 7 символов
            type: 'fertilizer'
        );

        $errors = $this->validator->validate($model);
        $this->assertGreaterThan(0, $errors);
        $this->assertEquals('color', $errors[0]->getPropertyPath());
    }

    /**
     * Проверка корректности работы с пустыми описаниями (null)
     */
    public function testUpdateWithNullDescriptions(): void
    {
        $model = new UpdateMarkerModel(
            groupId: 1,
            letter: 'P',
            color: '#FF00FF',
            type: 'pest',
            description: null,
            colorDescription: null
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }
}
