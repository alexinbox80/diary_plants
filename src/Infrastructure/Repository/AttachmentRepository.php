<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Attachment;
use DateTimeImmutable;

/**
 * @method Attachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attachment[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttachmentRepository extends AbstractRepository
{
    /**
     * Получить все вложения для определённой сущности.
     *
     * @param string $attachableType Например 'App\Domain\Entity\Plant'
     * @param int $attachableId
     * @return array
     */
    public function findByAttachable(string $attachableType, int $attachableId): array
    {
        return $this->entityManager->createQueryBuilder('a')
            ->andWhere('a.attachableType = :type')
            ->andWhere('a.attachableId = :id')
            ->setParameter('type', $attachableType)
            ->setParameter('id', $attachableId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Получить все вложения для определённой сущности, включая удалённые.
     *
     * @param class-string $attachableType Полное имя класса (например, App\Domain\Entity\Plant)
     * @param int $attachableId
     * @return Attachment[]
     */
    public function findByAttachableWithDeleted(string $attachableType, int $attachableId): array
    {
//        return $this->entityManager->createQueryBuilder('a')
//            ->andWhere('a.attachableType = :type')
//            ->andWhere('a.attachableId = :id')
//            ->setParameter('type', $attachableType)
//            ->setParameter('id', $attachableId)
//            ->getQuery()
//            ->getResult();

        return $this->entityManager->createQueryBuilder()
            ->select('a')
            ->from(Attachment::class, 'a')
            ->where('a.attachableType = :type')
            ->andWhere('a.attachableId = :id')
            ->andWhere('a.deletedAt IS NULL')
            ->setParameter('type', $attachableType)
            ->setParameter('id', $attachableId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Получить вложение по ID и типу сущности.
     */
    public function findOneByAttachable(string $attachableType, int $attachableId, int $attachmentId): ?Attachment
    {
        return $this->entityManager->createQueryBuilder('a')
            ->andWhere('a.attachableType = :type')
            ->andWhere('a.attachableId = :id')
            ->andWhere('a.id = :attachmentId')
            ->setParameter('type', $attachableType)
            ->setParameter('id', $attachableId)
            ->setParameter('attachmentId', $attachmentId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Удалить вложение по ID и типу сущности.
     */
    public function deleteByAttachable(string $attachableType, int $attachableId, int $attachmentId): void
    {
        $this->entityManager->createQueryBuilder('a')
            ->delete()
            ->andWhere('a.attachableType = :type')
            ->andWhere('a.attachableId = :id')
            ->andWhere('a.id = :attachmentId')
            ->setParameter('type', $attachableType)
            ->setParameter('id', $attachableId)
            ->setParameter('attachmentId', $attachmentId)
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return Attachment[]
     */
    public function getAttachmentsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('a')
            ->from(Attachment::class, 'a')
            ->orderBy('a.updatedAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery();

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $attachmentId
     * @return Attachment|null
     */
    public function find(int $attachmentId): ?Attachment
    {
        $repository = $this->entityManager->getRepository(Attachment::class);
        /** @var Attachment|null $attachment */
        $attachment = $repository->find($attachmentId);

        return $attachment;
    }

    /**
     * @return Attachment[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Attachment::class)->findAll();
    }

    /**
     * @param string $title
     * @return Attachment[]
     */
    public function findAttachmentsByTitle(string $title): array
    {
        return $this->entityManager->getRepository(Attachment::class)->findBy(['title' => $title]);
    }

    /**
     * @param string $filename
     * @return Attachment[]
     */
    public function findAttachmentsByFilename(string $filename): array
    {
        return $this->entityManager->getRepository(Attachment::class)->findBy(['filename' => $filename]);
    }

    /**
     * @param DateTimeImmutable $fileDate
     * @return Attachment[]
     */
    public function findAttachmentsByFileDate(DateTimeImmutable $fileDate): array
    {
        return $this->entityManager->getRepository(Attachment::class)->findBy(['file_date' => $fileDate]);
    }

    /**
     * @param string $path
     * @return Attachment[]
     */
    public function findAttachmentsByPath(string $path): array
    {
        return $this->entityManager->getRepository(Attachment::class)->findBy(['path' => $path]);
    }

    /**
     * @param Attachment $attachment
     * @return int
     */
    public function create(Attachment $attachment): int
    {
        return $this->store($attachment);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Attachment $attachment
     * @return void
     */
    public function remove(Attachment $attachment): void
    {
        $attachment->setDeletedAt();
        $this->flush();
    }
}
