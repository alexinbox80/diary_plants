<?php

namespace Unit\Domain\Model\Repotting;

use DateTimeImmutable;
use App\Domain\Model\Repotting\CreateRepottingModel;
use App\Domain\ValueObject\Enum\Repotting\PotMaterial;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Domain\ValueObject\Enum\Repotting\RepottingType;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateRepottingModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidModel(): void
    {
        // Берем реальные значения из Enum для теста
        $type = RepottingType::cases()[0]->value;
        $material = PotMaterial::cases()[0]->value;

        $model = new CreateRepottingModel(
            groupId: 1,
            plantId: 10,
            repottedAt: new DateTimeImmutable(),
            type: $type,
            potMaterial: $material,
            potSize: '15cm',
            comment: 'Все прошло успешно'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    public function testInvalidChoiceValues(): void
    {
        $model = new CreateRepottingModel(
            groupId: 1,
            plantId: 10,
            repottedAt: new DateTimeImmutable(),
            type: 'invalid_type', // Недопустимое значение
            potMaterial: 'invalid_material', // Недопустимое значение
            potSize: '15cm'
        );

        $errors = $this->validator->validate($model);

        $this->assertGreaterThanOrEqual(2, count($errors));

        $errorProperties = [];
        foreach ($errors as $error) {
            $errorProperties[] = $error->getPropertyPath();
        }

        $this->assertContains('type', $errorProperties);
        $this->assertContains('potMaterial', $errorProperties);
    }

    public function testPotSizeLengthValidation(): void
    {
        $type = RepottingType::cases()[0]->value;
        $material = PotMaterial::cases()[0]->value;

        $model = new CreateRepottingModel(
            groupId: 1,
            plantId: 10,
            repottedAt: new DateTimeImmutable(),
            type: $type,
            potMaterial: $material,
            potSize: str_repeat('a', 33) // Слишком длинная строка (max 32)
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(1, $errors);
        $this->assertEquals('potSize', $errors[0]->getPropertyPath());
    }

    public function testStrictTypeException(): void
    {
        $this->expectException(\TypeError::class);

        // PHP не позволит создать объект, если передать неверный тип в конструктор
        new CreateRepottingModel(
            groupId: 1,
            plantId: 10,
            repottedAt: 'not-a-date-object', // @phpstan-ignore-line
            type: 'standard',
            potMaterial: 'plastic',
            potSize: '10'
        );
    }
}
