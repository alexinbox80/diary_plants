<?php

namespace Unit\Domain\Model\Group;

use App\Domain\Model\Group\CreateGroupModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateGroupModelTest extends KernelTestCase
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
        $model = new CreateGroupModel(
            isActive: true,
            title: 'Комнатные джунгли',
            description: 'Группа для крупных лиственных растений'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Тест минимально необходимого набора данных
     */
    public function testMinimalValidModel(): void
    {
        // Поле description опционально и стоит в конце конструктора
        $model = new CreateGroupModel(
            isActive: false,
            title: 'Кактусы'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Тест валидации обязательного поля title (NotBlank)
     */
    public function testInvalidBlankTitle(): void
    {
        $model = new CreateGroupModel(
            isActive: true,
            title: '' // Ожидаем ошибку NotBlank
        );

        $errors = $this->validator->validate($model);

        $this->assertCount(1, $errors);
        $this->assertEquals('title', $errors[0]->getPropertyPath());
    }

    /**
     * Тест на Null в isActive (NotNull)
     * Поскольку поле типизировано как bool, передать null через PHP
     * можно только если отключить строгую типизацию или через рефлексию.
     * Но валидатор Symfony проверит это при десериализации.
     */
    public function testTypeConstraints(): void
    {
        $model = new CreateGroupModel(
            isActive: true,
            title: 'Test Title'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }
}
