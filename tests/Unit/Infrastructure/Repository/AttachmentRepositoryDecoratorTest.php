<?php

namespace Unit\Infrastructure\Repository;

use ReflectionClass;
use DateTimeImmutable;
use App\Domain\Entity\Plant;
use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Attachment;
use App\Domain\Model\Group\GroupModel;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Model\Plant\PlantModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\ValueObject\Attachment\FileInfo;
use App\Domain\Model\Attachment\AttachmentModel;
use App\Domain\Repository\AttachableResolverInterface;
use App\Domain\ValueObject\Attachment\DisplaySettings;
use App\Infrastructure\Repository\AttachmentRepository;
use App\Domain\ValueObject\Attachment\AttachableReference;
use App\Domain\ValueObject\Enum\Attachment\AttachableType;
use App\Infrastructure\Repository\PlantRepositoryDecorator;
use App\Infrastructure\Repository\GroupRepositoryDecorator;
use App\Infrastructure\Repository\OffspringRepositoryDecorator;
use App\Infrastructure\Repository\AttachmentRepositoryDecorator;

#[CoversClass(AttachmentRepositoryDecorator::class)]
class AttachmentRepositoryDecoratorTest extends TestCase
{
    private AttachmentRepository|MockObject $innerRepo;
    private GroupRepositoryDecorator|MockObject $groupRepo;
    private AttachableResolverInterface|MockObject $resolver;
    private PlantRepositoryDecorator|MockObject $plantRepo;
    private OffspringRepositoryDecorator|MockObject $offspringRepo;
    private AttachmentRepositoryDecorator $decorator;

    protected function setUp(): void
    {
        $this->innerRepo = $this->createMock(AttachmentRepository::class);
        $this->groupRepo = $this->createMock(GroupRepositoryDecorator::class);
        $this->resolver = $this->createMock(AttachableResolverInterface::class);
        $this->plantRepo = $this->createMock(PlantRepositoryDecorator::class);
        $this->offspringRepo = $this->createMock(OffspringRepositoryDecorator::class);

        $this->decorator = new AttachmentRepositoryDecorator(
            $this->innerRepo,
            $this->groupRepo,
            $this->resolver,
            $this->plantRepo,
            $this->offspringRepo
        );
    }

    #[Test]
    public function testFindModelReturnsCorrectModel(): void
    {
        $attachment = $this->createRealAttachment();
        $this->innerRepo->method('find')->willReturn($attachment);

        $this->groupRepo->method('toModel')->willReturn($this->createMock(GroupModel::class));

        $result = $this->decorator->findModel(1);

        $this->assertInstanceOf(AttachmentModel::class, $result);
    }

    #[Test]
    public function testToModelWithRelationsResolvesPlant(): void
    {
        $attachment = $this->createRealAttachment();
        $this->groupRepo->method('toModel')->willReturn($this->createMock(GroupModel::class));

        // Используем Reflection для создания чистого объекта Plant
        // чтобы AttachableType::fromClass() не выдал ошибку на имени мока
        $plantEntity = (new ReflectionClass(Plant::class))->newInstanceWithoutConstructor();

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->willReturn($plantEntity);

        $this->plantRepo->method('toModel')
            ->with($plantEntity)
            ->willReturn($this->createMock(PlantModel::class));

        $result = $this->decorator->toModel($attachment, true);

        $this->assertInstanceOf(AttachmentModel::class, $result);
    }

    /**
     * Создает реальный объект Attachment через Reflection, заполняя типизированные свойства
     */
    private function createRealAttachment(): Attachment
    {
        $reflection = new ReflectionClass(Attachment::class);
        $attachment = $reflection->newInstanceWithoutConstructor();

        $now = new DateTimeImmutable();

        // 1. Устанавливаем ID (setAccessible больше не нужен)
        $reflection->getProperty('id')->setValue($attachment, 1);

        // 2. Реальные DisplaySettings
        $display = new DisplaySettings('Title', 'Description', 'Alt Text', true);
        $reflection->getProperty('displaySettings')->setValue($attachment, $display);

        // 3. Реальный FileInfo через конструктор
        $fileInfo = new FileInfo(
            'image.jpg',
            '/uploads/attachments/',
            'image/jpeg',
            $now
        );
        $reflection->getProperty('fileInfo')->setValue($attachment, $fileInfo);

        // 4. Группа (мок)
        $reflection->getProperty('group')->setValue($attachment, $this->createMock(Group::class));

        // 5. Target (используем реальный объект)
        $target = new AttachableReference(10, AttachableType::PLANT);
        $reflection->getProperty('target')->setValue($attachment, $target);

        // 6. Даты трейтов в самом Attachment
        foreach (['createdAt', 'updatedAt'] as $dateProp) {
            $reflection->getProperty($dateProp)->setValue($attachment, $now);
        }

        return $attachment;
    }
}
