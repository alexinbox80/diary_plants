<?php

namespace Unit\Domain\Model\Fertilizer;

use App\Domain\Model\Fertilizer\UpdateFertilizerModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateFertilizerModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    /**
     * Проверка валидации при полном наборе корректных данных
     */
    public function testValidUpdateModel(): void
    {
        $model = new UpdateFertilizerModel(
            groupId: 2,
            markerId: 15,
            title: 'Осмокот Экзакт',
            amount: 1000,
            manufacturer: 'ICL Specialty Fertilizers',
            applicationRate: '4г на 1л субстрата',
            description: 'Пролонгированное действие',
            comment: 'Для декоративно-лиственных'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Проверка обязательных полей (NotBlank)
     */
    public function testInvalidBlankFields(): void
    {
        $model = new UpdateFertilizerModel(
            groupId: 1,
            markerId: 5,
            title: '',        // Ошибка
            amount: 200,
            manufacturer: ''  // Ошибка
        );

        $errors = $this->validator->validate($model);

        $this->assertGreaterThanOrEqual(2, count($errors));

        $invalidProperties = [];
        foreach ($errors as $error) {
            $invalidProperties[] = $error->getPropertyPath();
        }

        $this->assertContains('title', $invalidProperties);
        $this->assertContains('manufacturer', $invalidProperties);
    }

    /**
     * Проверка минимального набора данных для обновления
     */
    public function testMinimalUpdateModel(): void
    {
        // Необязательные поля (в конце конструктора) игнорируем
        $model = new UpdateFertilizerModel(
            groupId: 1,
            markerId: 1,
            title: 'Агрикола',
            amount: 50,
            manufacturer: 'Техноэкспорт'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Проверка типа данных (если они передаются в обход строгой типизации,
     * например через десериализатор)
     */
    public function testTypeConstraints(): void
    {
        $model = new UpdateFertilizerModel(
            groupId: 10,
            markerId: 2,
            title: 'Тест',
            amount: 500,
            manufacturer: 'Производитель',
            applicationRate: 'Здесь должна быть строка'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }
}

