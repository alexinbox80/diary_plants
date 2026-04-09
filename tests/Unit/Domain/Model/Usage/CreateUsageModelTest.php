<?php

namespace Unit\Domain\Model\Usage;

use DateTimeImmutable;
use App\Domain\Model\Usage\CreateUsageModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUsageModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidModel(): void
    {
        $model = new CreateUsageModel(
            groupId: 1,
            plantId: 10,
            useDate: new DateTimeImmutable('2024-06-15'),
            usableId: 55,
            usableType: 'stimulant',
            comment: 'Плановая обработка эпином'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    public function testInvalidBlankAndLengthFields(): void
    {
        // Создаем модель с нарушением NotBlank (пустые строки) и Length
        $model = new CreateUsageModel(
            groupId: 1,
            plantId: 10,
            useDate: new DateTimeImmutable(),
            usableId: 55,
            usableType: 'st', // Слишком короткий (min 3)
            comment: 'a'      // Слишком короткий (min 2)
        );

        $errors = $this->validator->validate($model);

        $this->assertGreaterThanOrEqual(2, count($errors));

        $errorProperties = [];
        foreach ($errors as $error) {
            $errorProperties[] = $error->getPropertyPath();
        }

        $this->assertContains('usableType', $errorProperties);
        $this->assertContains('comment', $errorProperties);
    }

    public function testCommentMaxLength(): void
    {
        $model = new CreateUsageModel(
            groupId: 1,
            plantId: 10,
            useDate: new DateTimeImmutable(),
            usableId: 55,
            usableType: 'stimulant',
            comment: str_repeat('Long comment ', 100) // Превышает 1024 символа
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(1, $errors);
        $this->assertEquals('comment', $errors[0]->getPropertyPath());
    }

    public function testPhpTypeSafety(): void
    {
        // Проверяем, что PHP не позволит передать строку в int поле
        $this->expectException(\TypeError::class);

        new CreateUsageModel(
            groupId: 'not-int', // @phpstan-ignore-line
            plantId: 10,
            useDate: new DateTimeImmutable(),
            usableId: 55,
            usableType: 'stimulant'
        );
    }
}
