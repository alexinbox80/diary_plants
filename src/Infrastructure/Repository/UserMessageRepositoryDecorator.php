<?php

namespace App\Infrastructure\Repository;

use Exception;
use InvalidArgumentException;
use App\Domain\Entity\UserMessage;
use App\Domain\Model\UserMessage\UserMessageModel;
use App\Domain\Repository\UserMessageRepositoryInterface;

class UserMessageRepositoryDecorator implements UserMessageRepositoryInterface
{
    public function __construct(
        private readonly UserMessageRepository $userMessageRepository,
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return UserMessageModel[]
     * @throws Exception
     */
    public function getUserMessagesPaginated(int $page, int $perPage): array
    {
        $userMessagesPaginated = $this->userMessageRepository->getUserMessagesPaginated($page, $perPage);

        if (!is_array($userMessagesPaginated['items'])) {
            throw new InvalidArgumentException('Expected array for User Messages');
        }

        $userMessagesModel = array_map(
            fn (UserMessage $userMessage): UserMessageModel => $this->toModel($userMessage),
            $userMessagesPaginated['items']
        );

        return [
            'userMessagesModel' => $userMessagesModel,
            'pagination' => $userMessagesPaginated['pagination']
        ];
    }

    /**
     * @param int $userMessageId
     * @return UserMessage|null
     */
    public function find(int $userMessageId): ?UserMessage
    {
        return $this->userMessageRepository->find($userMessageId);
    }

    /**
     * @param int $userMessageId
     * @return UserMessageModel|null
     */
    public function findModel(int $userMessageId): ?UserMessageModel
    {
        $userMessage = $this->userMessageRepository->find($userMessageId);

        return $this->toModel($userMessage);
    }

    /**
     * @return UserMessageModel[]
     */
    public function findAll(): array
    {
        $userMessages = $this->userMessageRepository->findAll();

        return array_map(
            fn (UserMessage $userMessage): UserMessageModel => $this->toModel($userMessage),
            $userMessages
        );
    }

    /**
     * @param ?int $userId
     * @return UserMessageModel[]
     */
    public function findAllByUserId(?int $userId = null): array
    {
        $userMessages = $this->userMessageRepository->findAllByUserId($userId);

        return array_map(
            fn (UserMessage $userMessage): UserMessageModel => $this->toModel($userMessage),
            $userMessages
        );
    }

    /**
     * @param ?int $groupId
     * @return UserMessage[]
     */
    public function findAllByGroupId(?int $groupId = null): array
    {
        $userMessages = $this->userMessageRepository->findAllByGroupId($groupId);

        return array_map(
            fn (UserMessage $userMessage): UserMessageModel => $this->toModel($userMessage),
            $userMessages
        );
    }

    /**
     * @param ?int $senderId
     * @return UserMessage[]
     */
    public function findAllBySenderId(?int $senderId = null): array
    {
        $userMessages = $this->userMessageRepository->findAllBySenderId($senderId);

        return array_map(
            fn (UserMessage $userMessage): UserMessageModel => $this->toModel($userMessage),
            $userMessages
        );
    }

    /**
     * @param UserMessage $userMessage
     * @return int
     */
    public function create(UserMessage $userMessage): int
    {
        return $this->userMessageRepository->create($userMessage);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->userMessageRepository->update();
    }

    /**
     * @param UserMessage $userMessage
     * @return void
     */
    public function remove(UserMessage $userMessage): void
    {
        $this->userMessageRepository->remove($userMessage);
    }

    /**
     * @param UserMessage $userMessage
     * @return UserMessageModel
     */
    public function toModel(UserMessage $userMessage): UserMessageModel
    {
        return UserMessageModel::fromEntity($userMessage);
    }
}
