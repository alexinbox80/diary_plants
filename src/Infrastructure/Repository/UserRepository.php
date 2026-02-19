<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\User;

class UserRepository extends AbstractRepository
{
    /**
     * @param int $page
     * @param int $perPage
     * @return User[]
     */
    public function getUsersPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('u')
            ->from(User::class, 'u')
            ->orderBy('u.updatedAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery();

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $userId
     * @return User|null
     */
    public function find(int $userId): ?User
    {
        $repository = $this->entityManager->getRepository(User::class);
        /** @var User|null $user */
        $user = $repository->find($userId);

        return $user;
    }

    /**
     * @return User[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(User::class)->findAll();
    }

    /**
     * @param int|null $groupId
     * @return User[]
     */
    public function findAllByGroupId(?int $groupId = null): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $qb = $queryBuilder->select('u')
            ->from(User::class, 'u')
            ->orderBy('u.email', 'ASC');

        if ($groupId !== null) {
            $qb->where('u.group = :groupId')
                ->setParameter('groupId', $groupId);
        }
        return  $qb->getQuery()->getResult();
    }

    /**
     * @param string $email
     * @return User[]
     */
    public function findUsersByEmail(string $email): array
    {
        return $this->entityManager->getRepository(User::class)->findBy(['email' => $email]);
    }

    /**
     * @param User $user
     * @return int
     */
    public function create(User $user): int
    {
        return $this->store($user);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param User $user
     * @return void
     */
    public function remove(User $user): void
    {
        $user->setDeletedAt();
        $this->flush();
    }
}
