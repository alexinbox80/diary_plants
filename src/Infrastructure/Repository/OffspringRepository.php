<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Offspring;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class OffspringRepository extends AbstractRepository
{
    private AttachmentRepository $attachmentRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        AttachmentRepository $attachmentRepository,
    )
    {
        parent::__construct($entityManager);

        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * Инкапсулирует логику загрузки и распределения вложений
     * @param Offspring[] $offsprings
     */
    private function loadAttachmentsForOffsprings(array $offsprings): void
    {
        if (empty($offsprings)) return;

        $ids = array_map(fn(Offspring $o) => $o->getId(), $offsprings);

        $attachments = $this->attachmentRepository->findAllByAttachable('offspring::class', $ids);

        $grouped = [];
        foreach ($attachments as $attachment) {
            $grouped[$attachment->getTarget()->getAttachableId()][] = $attachment;
        }

        foreach ($offsprings as $offspring) {
            $offspring->setLoadedAttachments($grouped[$offspring->getId()] ?? []);
        }
    }

    /**
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        return $queryBuilder
            ->select('o', 'p', 'g')
            ->from(Offspring::class, 'o')
            ->leftJoin('o.plant', 'p')
            ->leftJoin('o.group', 'g')
            ->where('p.deletedAt IS NULL')
            ->orderBy('o.updatedAt', 'DESC');
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws \Exception
     */
    public function getOffspringsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return Offspring[]
     * @throws \Exception
     */
    public function getOffspringsPaginatedWithAttachments(int $page, int $perPage): array
    {
        $result = $this->getOffspringsPaginated($page, $perPage);

        if (!isset($result['items'])) {
            throw new \RuntimeException('Pagination result is missing "items".');
        }

        if (!is_array($result['items'])) {
            throw new \InvalidArgumentException('"items" must be an array.');
        }

        $this->loadAttachmentsForOffsprings($result['items']);

        return $result;
    }

    /**
     * @param int $offspringId
     * @return Offspring|null
     */
    public function find(int $offspringId): ?Offspring
    {
        return $this->getBaseQueryBuilder()
            ->where('o.id = :id')
            ->setParameter('id', $offspringId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Offspring[]
     */
    public function findAll(): array
    {
        return $this->getBaseQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Offspring[]
     */
    public function findAllWithAttachments(): array
    {
        $offsprings = $this->findAll();

        $this->loadAttachmentsForOffsprings($offsprings);

        return $offsprings;
    }

    /**
     * @param string $mass
     * @return Offspring[]
     */
    public function findOffspringsByMass(string $mass): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('o.mass = :mass')
            ->setParameter('mass', $mass)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $flavor
     * @return Offspring[]
     */
    public function findOffspringsByFlavor(string $flavor): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('o.flavor = :flavor')
            ->setParameter('flavor', $flavor)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $color
     * @return Offspring[]
     */
    public function findOffspringsByColor(string $color): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('o.color = :color')
            ->setParameter('color', $color)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param  Offspring $offspring
     * @return int
     */
    public function create(Offspring $offspring): int
    {
        return $this->store($offspring);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Offspring $offspring
     * @return void
     */
    public function remove(Offspring $offspring): void
    {
        $offspring->setDeletedAt();
        $this->flush();
    }
}
