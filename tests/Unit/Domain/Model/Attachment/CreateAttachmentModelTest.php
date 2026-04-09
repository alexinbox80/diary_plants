<?php

namespace Unit\Domain\Model\Attachment;

use App\Domain\Model\Attachment\CreateAttachmentModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateAttachmentModelTest extends KernelTestCase
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
        $model = new CreateAttachmentModel(
            groupId: 1,
            isShown: true,
            alt: 'Описание фото',
            title: 'Заголовок фото',
            fileDate: new \DateTimeImmutable(),
            attachableId: 10,
            attachableType: 'plant',
            filename: 'photo.jpg',
            path: '/uploads/',
            mimeType: 'image/jpeg',
            description: 'Какое-то описание'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    /**
     * Тест валидации обязательных полей (NotBlank)
     */
    public function testInvalidBlankFields(): void
    {
        $model = new CreateAttachmentModel(
            groupId: 1,
            isShown: true,
            filename: null,     // Аргумент #3
            path: null,         // Аргумент #4
            mimeType: null,     // Аргумент #5
            alt: '',            // Теперь аргумент #6 встанет на свое место
            title: '',          // Аргумент #7
            fileDate: new \DateTimeImmutable(),
            attachableId: 10,
            attachableType: 'plant'
        );

        $errors = $this->validator->validate($model);

        $this->assertGreaterThanOrEqual(2, count($errors));

        $propertyPaths = [];
        foreach ($errors as $error) {
            $propertyPaths[] = $error->getPropertyPath();
        }

        $this->assertContains('alt', $propertyPaths);
        $this->assertContains('title', $propertyPaths);
    }

    /**
     * Тест на проверку типов данных
     */
    public function testTypeConstraints(): void
    {
        $model = new CreateAttachmentModel(
            groupId: 1,
            isShown: false,
            filename: null,     // Передаем явно
            path: null,
            mimeType: null,
            alt: 'Alt',
            title: 'Title',
            fileDate: new \DateTimeImmutable(),
            attachableId: 10,
            attachableType: 'plant'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }
}
