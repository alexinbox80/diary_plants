<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Group;

class GroupRepository extends AbstractRepository
{
    /**
     * @param int $page
     * @param int $perPage
     * @return Group[]
     */
    public function getGroupsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('g')
            ->from(Group::class, 'g')
            ->orderBy('g.updatedAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery();

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $groupId
     * @return Group|null
     */
    public function find(int $groupId): ?Group
    {
        $repository = $this->entityManager->getRepository(Group::class);
        /** @var Group|null $group */
        $group = $repository->find($groupId);

        return $group;
    }

    /**
     * @return Group[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Group::class)->findAll();
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
        return $this->entityManager->getRepository(Group::class)->findBy(['title' => $title]);
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
