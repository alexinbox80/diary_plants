<?php

namespace App\Infrastructure\Repository;

use DateTimeImmutable;
use Random\RandomException;
use App\Domain\Entity\User;
use Doctrine\ORM\QueryBuilder;
use App\Domain\ValueObject\User\RefreshToken;

class UserRepository extends AbstractRepository
{
    /**
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder->select('u', 'g')
            ->from(User::class, 'u')
            ->leftJoin('u.group', 'g')
            ->orderBy('u.updatedAt', 'DESC');
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return User[]
     * @throws \Exception
     */
    public function getUsersPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $userId
     * @return User|null
     */
    public function find(int $userId): ?User
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('u.id = :id')
            ->setParameter('id', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return User[]
     */
    public function findAll(): array
    {
        return $this->getBaseQueryBuilder()
            ->getQuery()
            ->getResult();
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
        return $this->getBaseQueryBuilder()
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $email
     * @return ?User|null
     */
    public function findUserByEmail(string $email): ?User
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $refreshToken
     * @return User|null
     */
    public function findUserByRefreshToken(string $refreshToken): ?User
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('u.refreshToken.token = :refreshToken')
            ->setParameter('refreshToken', $refreshToken)
            ->getQuery()
            ->getOneOrNullResult();
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

    /**
     * @param User $user
     * @return string
     * @throws RandomException
     */
    public function updateUserRefreshToken(User $user): string
    {
        $refreshToken = base64_encode(random_bytes(20));
        $expiresAt = new DateTimeImmutable('+30 days');
        $user->updateRefreshToken(new RefreshToken($refreshToken, $expiresAt));
        $this->flush();

        return $refreshToken;
    }

    /**
     * @param User $user
     * @return void
     */
    public function clearUserRefreshToken(User $user): void
    {
        $user->updateRefreshToken(new RefreshToken(null, null));
        $this->flush();
    }
}
