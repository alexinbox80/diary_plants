<?php

namespace App\Domain\Service;

use App\Domain\Entity\UserMessage;
use App\Domain\Model\UserMessage\UserMessageModel;
use App\Domain\Model\UserMessage\CreateUserMessageModel;
use App\Domain\Model\UserMessage\UpdateUserMessageModel;
use App\Domain\Repository\UserMessageRepositoryInterface;
use App\Controller\Web\Dashboard\UserMessage\EditUserMessage\Input\EditUserMessageDTO;
use App\Controller\Web\Dashboard\UserMessage\CreateUserMessage\Input\CreateUserMessageDTO;

class UserMessageService
{
    public function __construct(
        private readonly UserMessageRepositoryInterface $userMessageRepository
    ) {
    }

    /**
     * @param int $userMessageId
     * @return ?UserMessage
     */
    public function find(int $userMessageId): ?UserMessage
    {
        return $this->userMessageRepository->find($userMessageId);
    }

    /**
     * @return UserMessageModel[]
     */
    public function findAll(): array
    {
        return $this->userMessageRepository->findAll();
    }

    /**
     * @param int|null $userId
     * @return UserMessageModel[]
     */
    public function findAllByUserId(?int $userId = null): array
    {
        return $this->userMessageRepository->findAllByUserId($userId);
    }

    /**
     * @param int|null $groupId
     * @return UserMessageModel[]
     */
    public function findAllByGroupId(?int $groupId = null): array
    {
        return $this->userMessageRepository->findAllByGroupId($groupId);
    }

    /**
     * @param int|null $senderId
     * @return UserMessageModel[]
     */
    public function findAllBySenderId(?int $senderId = null): array
    {
        return $this->userMessageRepository->findAllBySenderId($senderId);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return UserMessageModel[]
     */
    public function getUserMessagesPaginated(int $page, int $perPage): array
    {
        return $this->userMessageRepository->getUserMessagesPaginated($page, $perPage);
    }

    /**
     * @param CreateUserMessageModel $createUserMessageModel
     * @return UserMessageModel
     */
    public function create(CreateUserMessageModel $createUserMessageModel): UserMessageModel
    {
        $userMessage = null;

        $this->userMessageRepository->create($userMessage);

        return $this->userMessageRepository->toModel($userMessage);
    }

    /**
     * @param UserMessage $userMessage
     * @param UpdateUserMessageModel $updateUserMessageModel
     * @return UserMessageModel
     */
    public function update(UserMessage $userMessage, UpdateUserMessageModel $updateUserMessageModel): UserMessageModel
    {
        $this->userMessageRepository->update();

        return $this->userMessageRepository->toModel($userMessage);
    }

    /**
     * @param int $userMessageId
     * @return void
     */
    public function removeById(int $userMessageId): void
    {
        $userMessage = $this->userMessageRepository->find($userMessageId);
        if ($userMessage !== null) {
            $this->userMessageRepository->remove($userMessage);
        }
    }
}
