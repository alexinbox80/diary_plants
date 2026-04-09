<?php

namespace Unit\Domain\Model\Usage;

use DateTimeImmutable;
use App\Domain\Model\Usage\UpdateUsageModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateUsageModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidUpdateModel(): void
    {
        $model = new UpdateUsageModel(
            groupId: 2,
            plantId: 15,
            useDate: new DateTimeImmutable('2024-07-20'),
            usableId: 101,
            usableType: 'fertilizer',
            comment: 'Обновленный комментарий к поливу'
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    public function testInvalidUsableTypeLength(): void
    {
        $model = new UpdateUsageModel(
            groupId: 1,
            plantId: 1,
            useDate: new DateTimeImmutable(),
            usableId: 1,
            usableType: 'xy', // Слишком короткий (min 3)
            comment: null
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(1, $errors);
        $this->assertEquals('usableType', $errors->get(0)->getPropertyPath());
    }

    public function testCommentConstraints(): void
    {
        // Тест на минимальную длину комментария (min 2)
        $model = new UpdateUsageModel(
            groupId: 1,
            plantId: 1,
            useDate: new DateTimeImmutable(),
            usableId: 1,
            usableType: 'stimulant',
            comment: 'f' // Слишком короткий
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(1, $errors);
        $this->assertEquals('comment', $errors->get(0)->getPropertyPath());
    }

    public function testTypeMismatchThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        // PHP не позволит передать null в NotBlank поле с типом int
        new UpdateUsageModel(
            groupId: 1,
            plantId: 1,
            useDate: new DateTimeImmutable(),
            usableId: 1,
            usableType: null, // @phpstan-ignore-line
            comment: null
        );
    }
}
