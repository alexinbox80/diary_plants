<?php

namespace Unit\Domain\Model\Pest;

use App\Domain\Model\Pest\UpdatePestModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdatePestModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    /**
     * Тест успешной валидации при полном обновлении данных
     */
    public function testValidUpdate(): void
    {
        $model = new UpdatePestModel(
            groupId: 2,
            markerId: 10,
            title: 'Актара (обновлено)',
            amount: 250,
            applicationRate: '4г на 5л',
            manufacturer: 'Syngenta',
            description: 'Новое описание обработки',
            comment: 'Повторить через 7 дней'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Проверка обязательных полей (NotBlank) при обновлении
     */
    public function testInvalidBlankTitleOnUpdate(): void
    {
        $model = new UpdatePestModel(
            groupId: 1,
            markerId: 5,
            title: '', // Ошибка: NotBlank
            amount: 10
        );

        $errors = $this->validator->validate($model);

        $this->assertCount(1, $errors);
        $this->assertEquals('title', $errors[0]->getPropertyPath());
    }

    /**
     * Проверка минимального набора данных (все опциональные поля в конце = null)
     */
    public function testMinimalUpdateModel(): void
    {
        $model = new UpdatePestModel(
            groupId: 1,
            markerId: 1,
            title: 'Фитоверм',
            amount: 2
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }
}
