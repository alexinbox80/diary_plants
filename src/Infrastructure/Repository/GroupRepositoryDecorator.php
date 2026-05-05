<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Group;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Repository\GroupRepositoryInterface;


class GroupRepositoryDecorator implements GroupRepositoryInterface
{
    public function __construct(
        private readonly GroupRepository $groupRepository
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array{groupsModel: GroupModel[], pagination: array}
     */
    public function getGroupsPaginated(int $page, int $perPage): array
    {
        $groupsPaginated = $this->groupRepository->getGroupsPaginated($page, $perPage);

        $groupsModel = array_map(
            fn (Group $group) => $this->toModel($group),
            $groupsPaginated['items']
        );

        return [
            'groupsModel' => $groupsModel,
            'pagination' => $groupsPaginated['pagination']
        ];
    }

    /**
     * @return GroupModel[]
     */
    public function getGroupsForForm(): array
    {
        $groups = $this->groupRepository->getgroupsForForm();

        return array_map(
            fn (Group $group): GroupModel => $this->toModel($group),
            $groups
        );
    }

    /**
     * @param int $groupId
     * @return Group|null
     */
    public function find(int $groupId): ?Group
    {
        return $this->groupRepository->find($groupId);
    }

    /**
     * @param int $groupId
     * @return GroupModel|null
     */
    public function findModel(int $groupId): ?GroupModel
    {
        $group = $this->groupRepository->find($groupId);

        return $this->toModel($group);
    }

    /**
     * @return groupModel[]
     */
    public function findAll(): array
    {
        $groups = $this->groupRepository->findAll();

        return array_map(
            fn (Group $group) => $this->toModel($group),
            $groups
        );
    }

    /**
     * @param int|null $groupId
     * @return groupModel[]
     */
    public function findAllByGroupId(?int $groupId = null): array
    {
        $groups = $this->groupRepository->findAllByGroupId($groupId);

        return array_map(
            fn (Group $group) => $this->toModel($group),
            $groups
        );
    }

    /**
     * @param string $title
     * @return GroupModel[]
     */
    public function findGroupsByTitle(string $title): array
    {
        $groups = $this->groupRepository->findGroupsByTitle($title);

        return array_map(
            fn (Group $group) => $this->toModel($group),
            $groups
        );
    }

    /**
     * @param Group $group
     * @return int
     */
    public function create(Group $group): int
    {
        return $this->groupRepository->create($group);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->groupRepository->update();
    }

    /**
     * @param Group $group
     * @return void
     */
    public function remove(Group $group): void
    {
        $this->groupRepository->remove($group);
    }

    public function toModel(Group $group): GroupModel
    {
        return GroupModel::fromEntity($group);
    }
}
