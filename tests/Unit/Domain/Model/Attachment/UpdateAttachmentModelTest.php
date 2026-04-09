<?php

namespace Unit\Domain\Model\Attachment;

use App\Domain\Model\Attachment\UpdateAttachmentModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateAttachmentModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidUpdateModel(): void
    {
        $model = new UpdateAttachmentModel(
            groupId: 1,
            isShown: true,
            filename: 'image.jpg',
            path: 'uploads/',
            mimeType: 'image/jpeg',
            alt: 'Обновленный текст',
            title: 'Обновленный заголовок',
            fileDate: new \DateTimeImmutable(),
            attachableId: 5,
            attachableType: 'plant',
            description: 'Новое описание'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    public function testInvalidBlankFields(): void
    {
        $model = new UpdateAttachmentModel(
            groupId: 1,
            isShown: true,
            filename: null,
            path: null,
            mimeType: null,
            alt: '', // Должно быть NotBlank
            title: '', // Должно быть NotBlank
            fileDate: new \DateTimeImmutable(),
            attachableId: 5,
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

    public function testTypeConstraintsWithNulls(): void
    {
        // Проверяем минимальный набор данных с явными null для средних аргументов
        $model = new UpdateAttachmentModel(
            groupId: 1,
            isShown: false,
            filename: null,
            path: null,
            mimeType: null,
            alt: 'Some Alt',
            title: 'Some Title',
            fileDate: new \DateTimeImmutable(),
            attachableId: 10,
            attachableType: 'group'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }
}
