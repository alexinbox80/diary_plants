<?php

namespace Unit\Domain\Model\Plant;

use App\Domain\ValueObject\Price;
use App\Domain\ValueObject\Enum\Currency;
use App\Domain\Model\Plant\UpdatePlantModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdatePlantModelTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidUpdateModel(): void
    {
        $currency = Currency::cases()[0];

        $model = new UpdatePlantModel(
            groupId: 5,
            title: 'Обновленный Фикус',
            room: 'Кухня',
            isShown: false,
            description: 'Новое описание',
            price: new Price(2500, $currency)
        );

        $errors = $this->validator->validate($model);

        $this->assertCount(0, $errors);
        $this->assertEquals(5, $model->groupId);
        $this->assertFalse($model->isShown);
    }

    public function testDefaultValues(): void
    {
        // Проверяем, что дефолтные значения из конструктора работают
        $model = new UpdatePlantModel(
            groupId: 1,
            title: 'Тест',
            room: 'Комната'
        );

        $this->assertTrue($model->isShown, 'По умолчанию isShown должен быть true');
        $this->assertFalse($model->isSold, 'По умолчанию isSold должен быть false');
        $this->assertNull($model->description);
    }

    public function testInvalidBlankFields(): void
    {
        $model = new UpdatePlantModel(
            groupId: 1,
            title: '', // Ошибка NotBlank
            room: ''   // Ошибка NotBlank
        );

        $errors = $this->validator->validate($model);

        $this->assertGreaterThanOrEqual(2, count($errors));

        $paths = [];
        foreach ($errors as $error) {
            $paths[] = $error->getPropertyPath();
        }

        $this->assertContains('title', $paths);
        $this->assertContains('room', $paths);
    }

    public function testTypeMismatchForGroupId(): void
    {
        $this->expectException(\TypeError::class);

        // Попытка передать строку в типизированный int свойство
        new UpdatePlantModel(
            groupId: 'not-an-int', // @phpstan-ignore-line
            title: 'Valid',
            room: 'Valid'
        );
    }
}
