<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Group;
use Doctrine\ORM\QueryBuilder;

class GroupRepository extends AbstractRepository
{
    /**
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder->select('g')
            ->from(Group::class, 'g')
            ->orderBy('g.updatedAt', 'DESC');
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return Group[]
     * @throws \Exception
     */
    public function getGroupsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);


        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $groupId
     * @return Group|null
     */
    public function find(int $groupId): ?Group
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('g.id = :id')
            ->setParameter('id', $groupId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Group[]
     */
    public function findAll(): array
    {
        return $this->getBaseQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int|null $groupId
     * @return Group[]
     */
    public function findAllByGroupId(?int $groupId = null): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $qb = $queryBuilder->select('g')
            ->from(Group::class, 'g')
            ->orderBy('g.title', 'ASC');

        if ($groupId !== null) {
            $qb->where('g.id = :groupId')
                ->setParameter('groupId', $groupId);
        }
        return  $qb->getQuery()->getResult();
    }

    /**
     * @param string $title
     * @return Group[]
     */
    public function findGroupsByTitle(string $title): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('g.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Group $group
     * @return int
     */
    public function create(Group $group): int
    {
        return $this->store($group);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Group $group
     * @return void
     */
    public function remove(Group $group): void
    {
        $group->setDeletedAt();
        $this->flush();
    }
}
