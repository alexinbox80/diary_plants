<?php

namespace Unit\Domain\Model\Repotting;

use DateTimeImmutable;
use App\Domain\Model\Repotting\UpdateRepottingModel;
use App\Domain\ValueObject\Enum\Repotting\PotMaterial;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Domain\ValueObject\Enum\Repotting\RepottingType;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateRepottingModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidUpdateModel(): void
    {
        // Берем реальные значения из Enum через метод value
        $type = RepottingType::cases()[0]->value;
        $material = PotMaterial::cases()[0]->value;

        $model = new UpdateRepottingModel(
            groupId: 2,
            plantId: 15,
            repottedAt: new DateTimeImmutable('now'),
            type: $type,
            potMaterial: $material,
            potSize: '20см',
            comment: 'Плановая пересадка'
        );

        $errors = $this->validator->validate($model);

        $this->assertCount(0, $errors);
    }

    public function testInvalidChoices(): void
    {
        $model = new UpdateRepottingModel(
            groupId: 1,
            plantId: 10,
            repottedAt: new DateTimeImmutable(),
            type: 'wrong_type',      // Невалидный выбор
            potMaterial: 'wood',     // Если 'wood' нет в PotMaterial::values()
            potSize: '10'
        );

        $errors = $this->validator->validate($model);

        $this->assertGreaterThanOrEqual(1, count($errors));

        $errorFields = [];
        foreach ($errors as $error) {
            $errorFields[] = $error->getPropertyPath();
        }

        $this->assertContains('type', $errorFields);
    }

    public function testPotSizeTooLong(): void
    {
        $type = RepottingType::cases()[0]->value;
        $material = PotMaterial::cases()[0]->value;

        $model = new UpdateRepottingModel(
            groupId: 1,
            plantId: 1,
            repottedAt: new DateTimeImmutable(),
            type: $type,
            potMaterial: $material,
            potSize: str_repeat('X', 33) // Лимит 32
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(1, $errors);
        $this->assertEquals('potSize', $errors[0]->getPropertyPath());
    }

    public function testStrictTypeSafety(): void
    {
        $this->expectException(\TypeError::class);

        // PHP не позволит передать null в NotBlank поле с типом int/string в конструкторе
        new UpdateRepottingModel(
            groupId: 1,
            plantId: 1,
            repottedAt: new DateTimeImmutable(),
            type: null, // @phpstan-ignore-line
            potMaterial: 'plastic',
            potSize: '5'
        );
    }
}
