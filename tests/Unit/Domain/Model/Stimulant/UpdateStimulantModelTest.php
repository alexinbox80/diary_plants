<?php

namespace Unit\Domain\Model\Stimulant;

use App\Domain\Model\Stimulant\UpdateStimulantModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateStimulantModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidUpdate(): void
    {
        // Используем именованные аргументы, чтобы пропустить необязательные в середине
        $model = new UpdateStimulantModel(
            groupId: 1,
            markerId: 5,
            title: 'Обновленный Стимулятор',
            amount: 1000,
            applicationRate: '2мл/л',
            manufacturer: 'Bayer AG',
            description: 'Новое описание',
            comment: null
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    public function testInvalidBlankFields(): void
    {
        $model = new UpdateStimulantModel(
            groupId: 1,
            markerId: 1,
            title: '',        // Ошибка NotBlank
            amount: 100,
            applicationRate: '2мл/л',
            manufacturer: ''  // Ошибка NotBlank
        );

        $errors = $this->validator->validate($model);

        $this->assertGreaterThanOrEqual(2, count($errors));

        $invalidPaths = array_map(fn($e) => $e->getPropertyPath(), iterator_to_array($errors));
        $this->assertContains('title', $invalidPaths);
        $this->assertContains('manufacturer', $invalidPaths);
    }

    public function testTypeSafetyForAmount(): void
    {
        $this->expectException(\TypeError::class);

        // PHP выкинет ошибку, так как ожидает int, а получает string
        new UpdateStimulantModel(
            groupId: 1,
            markerId: 1,
            title: 'Test',
            amount: 'five hundred', // @phpstan-ignore-line
            applicationRate: '2мл/л',
            manufacturer: 'Test'
        );
    }

    public function testMinimalUpdateWorks(): void
    {
        // Проверяем, что модель создается с минимальным набором данных
        // и обязательным manufacturer (используем именованные аргументы)
        $model = new UpdateStimulantModel(
            groupId: 1,
            markerId: 1,
            title: 'Минимум',
            amount: 10,
            applicationRate: '2мл/л',
            manufacturer: 'Завод №1'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
        $this->assertNull($model->description);
    }
}
