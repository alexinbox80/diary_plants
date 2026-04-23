<?php

namespace Unit\Domain\Service;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Service\AttachableResolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use App\Domain\Entity\Interfaces\AttachableInterface;

#[CoversClass(AttachableResolver::class)]
class AttachableResolverTest extends TestCase
{
    private EntityManagerInterface|MockObject $entityManager;
    private AttachableResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаем мок для EntityManager
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // Инициализируем тестируемый сервис
        $this->resolver = new AttachableResolver($this->entityManager);
    }

    #[Test]
    public function testResolveReturnsEntity(): void
    {
        $id = 1;
        $type = AttachableType::WATERING;

        // ИСПРАВЛЕНИЕ: указываем реальный класс, который возвращает ваш Enum для WATERING
        $entityClass = 'App\Domain\Entity\Watering';

        $entity = $this->createMock(AttachableInterface::class);

        $this->entityManager->expects($this->once())
            ->method('find')
            ->with($entityClass, $id) // Теперь строки совпадут
            ->willReturn($entity);

        $result = $this->resolver->resolve($type, $id);

        $this->assertSame($entity, $result);
    }

    #[Test]
    public function testResolveReturnsNullIfNotFound(): void
    {
        $this->entityManager->method('find')->willReturn(null);

        $result = $this->resolver->resolve(AttachableType::WATERING, 999);

        $this->assertNull($result);
    }

    #[Test]
    public function testResolveReturnsNullIfEntityDoesNotImplementInterface(): void
    {
        // Если find вернет объект, который НЕ реализует AttachableInterface
        $notAttachableEntity = new \stdClass();
        $this->entityManager->method('find')->willReturn($notAttachableEntity);

        $result = $this->resolver->resolve(AttachableType::WATERING, 1);

        $this->assertNull($result);
    }
}
