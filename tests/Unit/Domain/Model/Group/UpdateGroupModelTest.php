<?php

namespace Unit\Domain\Model\Group;

use App\Domain\Model\Group\UpdateGroupModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateGroupModelTest extends KernelTestCase
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
        $model = new UpdateGroupModel(
            isActive: false,
            title: 'Архивные растения',
            description: 'Группа для тех, кто больше не с нами'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Проверка, что при обновлении нельзя оставить заголовок пустым
     */
    public function testInvalidBlankTitleOnUpdate(): void
    {
        $model = new UpdateGroupModel(
            isActive: true,
            title: '' // Пустая строка для обязательного поля
        );

        $errors = $this->validator->validate($model);

        $this->assertCount(1, $errors);
        $this->assertEquals('title', $errors[0]->getPropertyPath());
        $this->assertEquals('Значение не должно быть пустым.', $errors[0]->getMessage());
    }

    /**
     * Проверка корректности работы с опциональным описанием
     */
    public function testUpdateWithNullDescription(): void
    {
        $model = new UpdateGroupModel(
            isActive: true,
            title: 'Новая категория',
            description: null
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }
}
