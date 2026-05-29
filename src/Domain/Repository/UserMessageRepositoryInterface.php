<?php

namespace App\Domain\Repository;

use App\Domain\Entity\UserMessage;
use App\Domain\Model\UserMessage\UserMessageModel;

interface UserMessageRepositoryInterface
{
    public function getUserMessagesPaginated(int $page, int $perPage): array;
    public function find(int $userMessageId): ?UserMessage;
    public function findModel(int $userMessageId): ?UserMessageModel;
    public function findAll(): array;
    public function findAllByUserId(?int $userId = null): array;
    public function findAllByGroupId(?int $groupId = null): array;
    public function findAllBySenderId(?int $senderId = null): array;
    public function create(UserMessage $userMessage): int;
    public function update(): void;
    public function remove(UserMessage $userMessage): void;
    public function toModel(UserMessage $userMessage): UserMessageModel;
}
