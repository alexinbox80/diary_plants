<?php

namespace Unit\Domain\Model\Watering;

use App\Domain\Model\Watering\CreateWateringModel;
use App\Domain\ValueObject\Enum\Watering\WaterType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Domain\ValueObject\Enum\Watering\WateringMethod;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateWateringModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidModel(): void
    {
        // Берем значения из реальных Enum
        $waterType = WaterType::cases()[0]->value;
        $method = WateringMethod::cases()[0]->value;

        $model = new CreateWateringModel(
            groupId: 1,
            markerId: 5,
            amount: 500,
            waterType: $waterType,
            wateringMethod: $method,
            temperature: '25°C',
            description: 'Полив по графику',
            comment: 'Все хорошо'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    public function testInvalidBlankFields(): void
    {
        // Проверяем NotBlank для строковых полей
        $model = new CreateWateringModel(
            groupId: 1,
            markerId: 5,
            amount: 500,
            waterType: '',        // Ошибка NotBlank
            wateringMethod: '',   // Ошибка NotBlank
            temperature: ''       // Ошибка NotBlank
        );

        $errors = $this->validator->validate($model);

        $this->assertGreaterThanOrEqual(3, count($errors));

        $invalidPaths = array_map(fn($e) => $e->getPropertyPath(), iterator_to_array($errors));
        $this->assertContains('waterType', $invalidPaths);
        $this->assertContains('wateringMethod', $invalidPaths);
        $this->assertContains('temperature', $invalidPaths);
    }

    public function testPhpTypeSafety(): void
    {
        // Проверяем строгую типизацию PHP для int полей
        $this->expectException(\TypeError::class);

        new CreateWateringModel(
            groupId: 'not-an-int', // TypeError на уровне конструктора
            markerId: 1,
            amount: 500,
            waterType: 'tap_water',
            wateringMethod: 'root',
            temperature: '20'
        );
    }

    public function testOptionalFieldsCanBeNull(): void
    {
        $model = new CreateWateringModel(
            groupId: 1,
            markerId: 1,
            amount: 100,
            waterType: 'distilled',
            wateringMethod: 'spray',
            temperature: 'room'
        // description и comment по умолчанию null
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
        $this->assertNull($model->description);
        $this->assertNull($model->comment);
    }
}
