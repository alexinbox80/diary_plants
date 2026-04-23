<?php

namespace Integration\Service;

use App\Domain\Service\ModelFactory;
use App\Domain\Model\Group\CreateGroupModel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ModelFactoryIntegrationTest extends KernelTestCase
{
    private ModelFactory $factory;

    protected function setUp(): void
    {
        // Запускаем ядро Symfony
        self::bootKernel();

        // Получаем сервис из контейнера
        $this->factory = self::getContainer()->get(ModelFactory::class);
    }

    public function testValidationFailsOnEmptyTitle(): void
    {
        // Ожидаем исключение валидации
        $this->expectException(ValidationFailedException::class);

        // Пытаемся создать модель с пустым заголовком (NotBlank должен сработать)
        $this->factory->makeModel(
            CreateGroupModel::class,
            isActive: true,
            title: '', // Пустая строка нарушит NotBlank
            description: 'Test'
        );
    }

    public function testValidationPassesOnValidData(): void
    {
        $model = $this->factory->makeModel(
            CreateGroupModel::class,
            isActive: true,
            title: 'Valid Title',
            description: 'Valid Description'
        );

        $this->assertInstanceOf(CreateGroupModel::class, $model);
        $this->assertEquals('Valid Title', $model->title);
    }
}
