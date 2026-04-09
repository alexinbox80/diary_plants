<?php

namespace Unit\Domain\Model\Plant;

use DateTimeImmutable;
use App\Domain\ValueObject\Price;
use App\Domain\ValueObject\Enum\Currency;
use App\Domain\Model\Plant\CreatePlantModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreatePlantModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidModel(): void
    {
        $currency = Currency::cases()[0];

        $model = new CreatePlantModel(
            groupId: 1,
            title: 'Ficus',
            room: 'Living Room',
            isShown: true,
            purchaseDate: new DateTimeImmutable(),
            price: new Price(1000, $currency)
        );

        $errors = $this->validator->validate($model);
        $this->assertCount(0, $errors);
    }

    public function testInvalidBlankFields(): void
    {
        $model = new CreatePlantModel(
            groupId: 1,
            title: '', // Ошибка NotBlank
            room: '',  // Ошибка NotBlank
            isShown: true
        );

        $errors = $this->validator->validate($model);

        $this->assertGreaterThanOrEqual(2, count($errors));

        $errorProperties = [];
        foreach ($errors as $error) {
            $errorProperties[] = $error->getPropertyPath();
        }

        $this->assertContains('title', $errorProperties);
        $this->assertContains('room', $errorProperties);
    }

    public function testPhpStrictTypeValidation(): void
    {
        // Проверяем, что PHP не позволит передать строку вместо объекта Price
        $this->expectException(\TypeError::class);

        new CreatePlantModel(
            groupId: 1,
            title: 'Test',
            room: 'Test',
            isShown: true,
            price: 'not-a-price-object' // @phpstan-ignore-line
        );
    }
}
