<?php

namespace App\Infrastructure\Repository;

use Exception;
use Doctrine\ORM\QueryBuilder;
use App\Domain\Entity\UserMessage;

class UserMessageRepository extends AbstractRepository
{
    /**
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder->select('um')
            ->from(UserMessage::class, 'um')
            ->orderBy('um.updatedAt', 'DESC');
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return UserMessage[]
     * @throws Exception
     */
    public function getUserMessagesPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $userMessageId
     * @return UserMessage|null
     */
    public function find(int $userMessageId): ?UserMessage
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('um.id = :id')
            ->setParameter('id', $userMessageId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return UserMessage[]
     */
    public function findAll(): array
    {
        return $this->getBaseQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param ?int $userId
     * @return UserMessage[]
     */
    public function findAllByUserId(?int $userId = null): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $qb = $queryBuilder->select('um')
            ->from(UserMessage::class, 'um')
            ->orderBy('um.translationKey', 'ASC');

        if ($userId !== null) {
            $qb->where('um.userId = :userId')
                ->setParameter('userId', $userId);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param ?int $groupId
     * @return UserMessage[]
     */
    public function findAllByGroupId(?int $groupId = null): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $qb = $queryBuilder->select('um')
            ->from(UserMessage::class, 'um')
            ->orderBy('um.translationKey', 'ASC');

        if ($groupId !== null) {
            $qb->where('um.groupId = :groupId')
                ->setParameter('groupId', $groupId);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param ?int $senderId
     * @return UserMessage[]
     */
    public function findAllBySenderId(?int $senderId = null): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $qb = $queryBuilder->select('um')
            ->from(UserMessage::class, 'um')
            ->orderBy('um.translationKey', 'ASC');

        if ($senderId !== null) {
            $qb->where('um.senderId = :senderId')
                ->setParameter('senderId', $senderId);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param UserMessage $userMessage
     * @return int
     */
    public function create(UserMessage $userMessage): int
    {
        return $this->store($userMessage);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param UserMessage $userMessage
     * @return void
     */
    public function remove(UserMessage $userMessage): void
    {
        $userMessage->setDeletedAt();
        $this->flush();
    }
}
