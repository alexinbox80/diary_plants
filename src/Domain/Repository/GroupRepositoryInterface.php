<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Group;
use App\Domain\Model\Group\GroupModel;

interface GroupRepositoryInterface
{
    public function getGroupsPaginated(int $page, int $perPage): array;
    public function getGroupsForForm(): array;
    public function find(int $groupId): ?Group;
    public function findModel(int $groupId): ?GroupModel;
    public function findAll(): array;
    public function findAllByGroupId(?int $groupId = null): array;
    public function findGroupsByTitle(string $title): array;
    public function create(Group $group): int;
    public function update(): void;
    public function remove(Group $group): void;
    public function toModel(Group $group): GroupModel;
}
