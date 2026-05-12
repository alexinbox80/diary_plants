<?php

namespace Unit\Domain\Service;

use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use App\Domain\Service\GroupService;
use App\Domain\Service\ModelFactory;
use App\Domain\Model\Group\GroupModel;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use App\Domain\Model\Group\CreateGroupModel;
use App\Domain\Model\Group\UpdateGroupModel;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Repository\GroupRepositoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[CoversClass(GroupService::class)]
class GroupServiceTest extends TestCase
{
    private GroupRepositoryInterface|MockObject $repository;
    private ModelFactory|MockObject $modelFactory;
    private TranslatorInterface|MockObject $translator; // Добавляем свойство
    private GroupService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(GroupRepositoryInterface::class);
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->translator = $this->createMock(TranslatorInterface::class); // Создаем мок

        // Теперь передаем 3 аргумента в конструктор
        $this->service = new GroupService(
            $this->repository,
            $this->modelFactory,
            $this->translator
        );
    }

    #[Test]
    public function testGetChoicesForFormChoiceType(): void
    {
        $group = $this->createMock(GroupModel::class);
        $group->method('getTitle')->willReturn('Тестовая');
        $group->method('getId')->willReturn(77);

        $this->repository->method('getGroupsForForm')->willReturn([$group]);

        // Настраиваем имитацию перевода: проверяем, что в транслятор уходит нужный ключ и параметры
        $this->translator->expects($this->once())
            ->method('trans')
            ->with('group.label.format', ['%title%' => 'Тестовая', '%id%' => 77])
            ->willReturn('Тестовая :: 77');

        $result = $this->service->getChoicesForFormChoiceType();

        $this->assertSame(['Тестовая :: 77' => 77], $result);
    }

    #[Test]
    public function testFindAllByGroupIdFormatsChoices(): void
    {
        // Подготовка моков моделей
        $group1 = $this->createMock(GroupModel::class);
        $group1->method('getTitle')->willReturn('Комнатные');
        $group1->method('getId')->willReturn(1);

        $group2 = $this->createMock(GroupModel::class);
        $group2->method('getTitle')->willReturn('Садовые');
        $group2->method('getId')->willReturn(2);

        $this->repository->expects($this->once())
            ->method('findAllByGroupId')
            ->with(null)
            ->willReturn([$group1, $group2]);

        $result = $this->service->findAllByGroupId();

        // Проверяем формат [title => id]
        $expected = [
            'Комнатные' => 1,
            'Садовые' => 2
        ];
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function testCreateSuccess(): void
    {
        $model = new CreateGroupModel(
            isActive: true,
            title: 'Новая группа',
            description: 'Описание'
        );

        $groupModel = $this->createMock(GroupModel::class);

        $this->repository->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(Group::class));

        $this->repository->expects($this->once())
            ->method('toModel')
            ->willReturn($groupModel);

        $result = $this->service->create($model);

        $this->assertSame($groupModel, $result);
    }

    #[Test]
    public function testUpdateSuccess(): void
    {
        $groupEntity = $this->createMock(Group::class);
        $updateModel = new UpdateGroupModel(
            isActive: false,
            title: 'Измененный заголовок',
            description: 'Новое описание'
        );

        $groupModel = $this->createMock(GroupModel::class);

        // Проверяем, что в сущности вызывается метод смены полей
        $groupEntity->expects($this->once())->method('changeFields');

        $this->repository->expects($this->once())->method('update');
        $this->repository->method('toModel')->willReturn($groupModel);

        $result = $this->service->update($groupEntity, $updateModel);

        $this->assertSame($groupModel, $result);
    }

    #[Test]
    public function testRemoveById(): void
    {
        $id = 10;
        $group = $this->createMock(Group::class);

        $this->repository->method('find')->with($id)->willReturn($group);
        $this->repository->expects($this->once())->method('remove')->with($group);

        $this->service->removeById($id);
    }
}
