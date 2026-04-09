<?php

namespace Unit\Domain\Model\Fertilizer;

use App\Domain\Model\Fertilizer\CreateFertilizerModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateFertilizerModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    /**
     * Тест успешной валидации со всеми корректными данными
     */
    public function testValidModel(): void
    {
        $model = new CreateFertilizerModel(
            groupId: 1,
            markerId: 10,
            title: 'Биогумус',
            amount: 500,
            manufacturer: 'Органик Микс',
            applicationRate: '5г на литр',
            description: 'Для рассады',
            comment: 'Применить весной'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Тест валидации обязательных полей (NotBlank)
     */
    public function testInvalidBlankFields(): void
    {
        // Проверяем только обязательные поля, оставляя необязательные по умолчанию (null)
        // Но передаем пустые строки в обязательные текстовые поля
        $model = new CreateFertilizerModel(
            groupId: 1,
            markerId: 10,
            title: '',        // Ошибка: NotBlank
            amount: 500,
            manufacturer: ''  // Ошибка: NotBlank
        );

        $errors = $this->validator->validate($model);

        $this->assertGreaterThanOrEqual(2, count($errors));

        $propertyPaths = [];
        foreach ($errors as $error) {
            $propertyPaths[] = $error->getPropertyPath();
        }

        $this->assertContains('title', $propertyPaths);
        $this->assertContains('manufacturer', $propertyPaths);
    }

    /**
     * Тест минимально необходимого набора данных
     */
    public function testMinimalValidModel(): void
    {
        // Здесь не нужно передавать описание и комментарий, так как они в конце и = null
        $model = new CreateFertilizerModel(
            groupId: 1,
            markerId: 10,
            title: 'Удобрение',
            amount: 100,
            manufacturer: 'Производитель'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }
}

