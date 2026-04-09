<?php

namespace Unit\Domain\Model\Watering;

use App\Domain\Model\Watering\UpdateWateringModel;
use App\Domain\ValueObject\Enum\Watering\WaterType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Domain\ValueObject\Enum\Watering\WateringMethod;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateWateringModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidUpdate(): void
    {
        // Используем реальные значения из Enum через именованные аргументы
        $model = new UpdateWateringModel(
            groupId: 2,
            markerId: 10,
            amount: 300,
            waterType: WaterType::cases()[0]->value,
            wateringMethod: WateringMethod::cases()[0]->value,
            temperature: '22°C',
            description: 'Обновленное описание',
            comment: 'Все ок'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    public function testInvalidBlankFields(): void
    {
        $model = new UpdateWateringModel(
            groupId: 1,
            markerId: 1,
            amount: 100,
            waterType: '',        // NotBlank
            wateringMethod: '',   // NotBlank
            temperature: ''       // NotBlank
        );

        $errors = $this->validator->validate($model);
        $this->assertGreaterThanOrEqual(3, count($errors));

        $invalidPaths = array_map(fn($e) => $e->getPropertyPath(), iterator_to_array($errors));
        $this->assertContains('waterType', $invalidPaths);
        $this->assertContains('wateringMethod', $invalidPaths);
        $this->assertContains('temperature', $invalidPaths);
    }

    public function testTypeSafety(): void
    {
        $this->expectException(\TypeError::class);

        // Проверяем защиту на уровне PHP (string вместо int)
        new UpdateWateringModel(
            groupId: 1,
            markerId: 1,
            amount: 'not-int', // @phpstan-ignore-line
            waterType: 'tap',
            wateringMethod: 'root',
            temperature: '20'
        );
    }

    public function testMinimalUpdateWorks(): void
    {
        $model = new UpdateWateringModel(
            groupId: 1,
            markerId: 1,
            amount: 50,
            waterType: 'distilled',
            wateringMethod: 'spray',
            temperature: 'room'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
        $this->assertNull($model->description);
        $this->assertNull($model->comment);
    }
}
