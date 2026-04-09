<?php

namespace Unit\Domain\Model\Marker;

use App\Domain\Model\Marker\CreateMarkerModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateMarkerModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    /**
     * Тест успешной валидации с корректными данными
     */
    public function testValidModel(): void
    {
        $model = new CreateMarkerModel(
            groupId: 1,
            letter: 'NPK',
            color: '#FF5733',
            type: 'fertilizer',
            description: 'Main Fertilizer',
            colorDescription: 'Orange'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Тест на слишком длинную букву (макс 3)
     */
    public function testInvalidLetterLength(): void
    {
        $model = new CreateMarkerModel(
            groupId: 1,
            letter: 'LONG', // Ошибка: макс 3
            color: '#000000',
            type: 'pest'
        );

        $errors = $this->validator->validate($model);
        $this->assertGreaterThan(0, $errors);
        $this->assertEquals('letter', $errors[0]->getPropertyPath());
        $this->assertEquals('Letter must be exactly one character long.', $errors[0]->getMessage());
    }

    /**
     * Тест на некорректную длину цвета (должно быть ровно 7)
     */
    public function testInvalidColorLength(): void
    {
        $model = new CreateMarkerModel(
            groupId: 1,
            letter: 'M',
            color: '#FFF', // Ошибка: должно быть 7 символов
            type: 'watering'
        );

        $errors = $this->validator->validate($model);
        $this->assertGreaterThan(0, $errors);
        $this->assertEquals('color', $errors[0]->getPropertyPath());
    }

    /**
     * Тест минимального набора данных (description и colorDescription = null)
     */
    public function testMinimalValidModel(): void
    {
        $model = new CreateMarkerModel(
            groupId: 1,
            letter: 'W',
            color: '#0000FF',
            type: 'watering'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }
}
