<?php

namespace Unit\Domain\Model\Stimulant;

use App\Domain\Model\Stimulant\CreateStimulantModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateStimulantModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidModel(): void
    {
        $model = new CreateStimulantModel(
            groupId: 1,
            markerId: 10,
            title: 'Эпин-Экстра',
            amount: 500,
            applicationRate: '1мл на 5л',
            manufacturer: 'НЭСТ М',
            description: 'Стимулятор роста',
            comment: 'Хранить в темноте'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    public function testInvalidBlankFields(): void
    {
        $model = new CreateStimulantModel(
            groupId: 1,
            markerId: 2,
            title: '',
            amount: 100,
            applicationRate: null,
            manufacturer: ''
        );

        $errors = $this->validator->validate($model);
        $this->assertGreaterThanOrEqual(2, count($errors));
    }

    public function testPhpTypeSafety(): void
    {
        // Проверяем строгую типизацию PHP для int полей
        $this->expectException(\TypeError::class);

        new CreateStimulantModel(
            groupId: 'not-an-integer', // @phpstan-ignore-line
            markerId: 1,
            title: 'Test',
            amount: 100,
            manufacturer: 'Test'
        );
    }

    public function testOptionalFieldsCanBeNull(): void
    {
        $model = new CreateStimulantModel(
            groupId: 1,
            markerId: 1,
            title: 'Минимальный набор',
            amount: 50,
            applicationRate: null,
            manufacturer: 'Завод'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }
}
