<?php

namespace App\Domain\Repository;

use App\Domain\Entity\User;
use App\Domain\Model\User\UserModel;

interface UserRepositoryInterface
{
    public function getUsersPaginated(int $page, int $perPage): array;
    public function find(int $userId): ?User;
    public function findModel(int $userId): ?UserModel;
    public function findAll(): array;
    public function findAllByGroupId(?int $groupId = null): array;
    public function findUsersByEmail(string $email): array;
    public function create(User $user): int;
    public function update(): void;
    public function remove(User $user): void;
    public function toModel(User $user): UserModel;
}
