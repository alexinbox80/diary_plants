<?php

namespace App\Domain\Service;

use App\Domain\Entity\Group;
use InvalidArgumentException;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Group\CreateGroupModel;
use App\Domain\Model\Group\UpdateGroupModel;
use App\Domain\Repository\GroupRepositoryInterface;
use App\Controller\Web\Dashboard\Group\EditGroup\Input\EditGroupDTO;
use App\Controller\Web\Dashboard\Group\CreateGroup\Input\CreateGroupDTO;

class GroupService
{
    public function __construct(
        private readonly GroupRepositoryInterface $groupRepository,
        private readonly ModelFactory $modelFactory,
    ) {
    }

    /**
     * @param int|null $groupId
     * @return GroupModel[]
     */
    public function getChoicesForChoiceType(?int $groupId = null): array
    {
        return $this->findAllByGroupId($groupId);
    }

    /**
     * @param int|null $groupId
     * @return GroupModel[]
     */
    public function findAllByGroupId(?int $groupId = null): array
    {
        // Получаем список выбора: [title => id]
        $choices = [];
        $plantModels = $this->groupRepository->findAllByGroupId($groupId);
        foreach ($plantModels as $plant) {
            $choices[$plant->getTitle()] = $plant->getId();
        }

        return $choices;
    }

    /**
     * @param int $groupId
     * @return ?Group
     */
    public function find(int $groupId): ?Group
    {
        return $this->groupRepository->find($groupId);
    }

    /**
     * @return GroupModel[]
     */
    public function findAll(): array
    {
        return $this->groupRepository->findAll();
    }

    /**
     * @param string $title
     * @return GroupModel[]
     */
    public function findGroupsByTitle(string $title): array
    {
        return $this->groupRepository->findGroupsByTitle($title);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws InvalidArgumentException
 */
    public function getGroupsPaginated(int $page, int $perPage): array
    {
        return $this->groupRepository->getGroupsPaginated($page, $perPage);
    }

    /**
     * @param CreateGroupModel $createGroupModel
     * @return GroupModel
     * @throws InvalidArgumentException
     */
    public function create(CreateGroupModel $createGroupModel): GroupModel
    {
        $group = new Group(
            $createGroupModel->isActive,
            $createGroupModel->title,
            $createGroupModel->description
        );

        $this->groupRepository->create($group);

        return $this->groupRepository->toModel($group);
    }

    /**
     * @param CreateGroupDTO $dto
     * @return GroupModel
     */
    public function createFromCreateGroupDTO(CreateGroupDTO $dto): GroupModel
    {
        $model = $this->modelFactory->makeModel(
            CreateGroupModel::class,
            $dto->isActive,
            $dto->title,
            $dto->description,
        );

        return $this->create($model);
    }

    /**
     * @param Group $group
     * @param UpdateGroupModel $updateGroupModel
     * @return GroupModel
     * @throws InvalidArgumentException
     */
    public function update(Group $group, UpdateGroupModel $updateGroupModel): GroupModel
    {
        $group->changeFields(
            $updateGroupModel->isActive,
            $updateGroupModel->title,
            $updateGroupModel->description,
        );

        $this->groupRepository->update();

        return $this->groupRepository->toModel($group);
    }

    /**
     * @param Group $group
     * @param EditGroupDTO $dto
     * @return void
     */
    public function updateFromEditGroupDTO(Group $group, EditGroupDTO $dto): void
    {
        // Создаём модель обновления
        $model = $this->modelFactory->makeModel(
            UpdateGroupModel::class,
            $dto->isActive,
            $dto->title,
            $dto->description,
        );

        // Выполняем обновление
        $this->update($group, $model);
    }

    /**
     * @param int $groupId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $groupId): void
    {
        $group = $this->groupRepository->find($groupId);
        $this->groupRepository->remove($group);
    }

    /**
     * @param Group $group
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeGroup(Group $group): void
    {
        $this->groupRepository->remove($group);
    }
}
