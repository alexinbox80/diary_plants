<?php

namespace Unit\Infrastructure\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Model\Group\GroupModel;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use Doctrine\Common\Collections\ArrayCollection;
use App\Infrastructure\Repository\GroupRepository;
use App\Infrastructure\Repository\GroupRepositoryDecorator;

#[CoversClass(GroupRepositoryDecorator::class)]
class GroupRepositoryDecoratorTest extends TestCase
{
    private GroupRepository|MockObject $innerRepository;
    private GroupRepositoryDecorator $decorator;

    protected function setUp(): void
    {
        $this->innerRepository = $this->createMock(GroupRepository::class);
        $this->decorator = new GroupRepositoryDecorator($this->innerRepository);
    }

    #[Test]
    public function testFindModelReturnsCorrectModel(): void
    {
        $groupId = 1;
        $group = $this->createGroupMock($groupId);

        $this->innerRepository->method('find')->with($groupId)->willReturn($group);

        $result = $this->decorator->findModel($groupId);

        $this->assertInstanceOf(GroupModel::class, $result);
        $this->assertSame($groupId, $result->getId());
    }

    #[Test]
    public function testFindAllReturnsArrayOfModels(): void
    {
        $group = $this->createGroupMock(1);
        $this->innerRepository->method('findAll')->willReturn([$group]);

        $results = $this->decorator->findAll();

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertInstanceOf(GroupModel::class, $results[0]);
    }

    #[Test]
    public function testGetGroupsPaginatedReturnsFormattedArray(): void
    {
        $group = $this->createGroupMock(1);
        $paginationData = [
            'items' => [$group],
            'pagination' => ['total' => 1, 'page' => 1]
        ];

        $this->innerRepository->method('getGroupsPaginated')
            ->with(1, 10)
            ->willReturn($paginationData);

        $result = $this->decorator->getGroupsPaginated(1, 10);

        $this->assertArrayHasKey('groupsModel', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertInstanceOf(GroupModel::class, $result['groupsModel'][0]);
        $this->assertSame($paginationData['pagination'], $result['pagination']);
    }

    /**
     * Создает мок Group для PHPUnit 12 через Stub
     */
    private function createGroupMock(int $id): MockObject
    {
        $group = $this->getMockBuilder(GroupStub::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getId',
                'getTitle',
                'getDescription',
                'isActive',
                'getCreatedAt',
                'getUpdatedAt',
                'getPlants' // Добавлено на случай, если модель запрашивает коллекции
            ])
            ->getMock();

        $group->method('getId')->willReturn($id);
        $group->method('getTitle')->willReturn('Тестовая группа');
        $group->method('getDescription')->willReturn('Описание');
        $group->method('isActive')->willReturn(true);
        $group->method('getPlants')->willReturn(new ArrayCollection());

        $now = new DateTimeImmutable();
        $group->method('getCreatedAt')->willReturn($now);
        $group->method('getUpdatedAt')->willReturn($now);

        return $group;
    }
}

/**
 * Стаб-класс для сущности Group.
 * Необходим для того, чтобы PHPUnit 12 мог перехватить методы Трейтов
 * и избежать обращения к неинициализированным свойствам родителя.
 */
abstract class GroupStub extends Group
{
    public function getId(): int { return 0; }
    public function getTitle(): string { return ''; }
    public function getDescription(): ?string { return null; }
    public function isActive(): bool { return true; }
    public function getCreatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
    public function getUpdatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
    public function getPlants(): Collection { return new ArrayCollection(); }
}
