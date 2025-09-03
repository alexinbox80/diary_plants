<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Attachment;

/**
 * @method Attachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attachment[]    findAll()
 * @method Attachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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
     */
    public function findByAttachableWithDeleted(string $attachableType, int $attachableId): array
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
}
