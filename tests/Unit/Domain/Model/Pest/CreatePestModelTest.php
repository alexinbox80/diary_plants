<?php

namespace Unit\Domain\Model\Pest;

use App\Domain\Model\Pest\CreatePestModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreatePestModelTest extends KernelTestCase
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
        $model = new CreatePestModel(
            groupId: 1,
            markerId: 5,
            title: 'Актара',
            amount: 10,
            applicationRate: '1г на 10л',
            manufacturer: 'Syngenta',
            description: 'Инсектицид широкого спектра',
            comment: 'Для полива и опрыскивания'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Тест валидации обязательных полей (NotBlank)
     */
    public function testInvalidBlankFields(): void
    {
        $model = new CreatePestModel(
            groupId: 1,
            markerId: 5,
            title: '', // Ошибка: NotBlank
            amount: 10
        );

        $errors = $this->validator->validate($model);

        $this->assertGreaterThanOrEqual(1, count($errors));
        $this->assertEquals('title', $errors[0]->getPropertyPath());
    }

    /**
     * Тест минимального набора данных (опциональные поля = null)
     */
    public function testMinimalValidModel(): void
    {
        $model = new CreatePestModel(
            groupId: 1,
            markerId: 5,
            title: 'Фитоверм',
            amount: 4
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }
}
