<?php

namespace App\Infrastructure\Repository;

use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use App\Domain\Entity\Attachment;
use App\Domain\ValueObject\Enum\Attachment\AttachableType;

/**
 * @method Attachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attachment[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttachmentRepository extends AbstractRepository
{
    /**
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder->select('a', 'g')
            ->from(Attachment::class, 'a')
            ->leftJoin('a.group', 'g')
            ->orderBy('a.updatedAt', 'DESC');
    }

    /**
     * @param Attachment[] $attachments
     * @return void
     */
    private function preloadTargets(array $attachments): void
    {
        $map = [];
        foreach ($attachments as $attachment) {
            $target = $attachment->getTarget();
            // Наш Enum из поля attachableType
            $type = $target->getAttachableType();
            if ($type instanceof AttachableType) {
                $map[$type->value][] = $target->getAttachableId();
            }
        }

        foreach ($map as $typeAlias => $ids) {
            $ids = array_unique(array_filter($ids));
            if (empty($ids)) continue;

            $enumCase = AttachableType::from($typeAlias);
            $className = $enumCase->getClass($enumCase->value);

            // Загружаем пачкой все сущности этого типа.
            // Doctrine положит их в UnitOfWork (Identity Map).
            $this->entityManager->getRepository($className)->findBy(['id' => $ids]);
        }
    }

    /**
     * Получить все вложения для определённой сущности.
     *
     * @param string $attachableType Например 'App\Domain\Entity\Plant'
     * @param int $attachableId
     * @return array
     */
    public function findByAttachable(string $attachableType, int $attachableId): array
    {
        return $this->getBaseQueryBuilder()
            ->where('a.target.attachableType = :type')
            ->andWhere('a.target.attachableId = :id')
            ->setParameter('type', $attachableType)
            ->setParameter('id', $attachableId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $attachableType
     * @param array $attachableId
     * @return array
     */
    public function findAllByAttachable(string $attachableType, array $attachableId): array
    {
        return $this->getBaseQueryBuilder()
            ->where('a.target.attachableId IN (:ids)')
            ->andWhere('a.target.attachableType = :type')
            ->setParameter('ids', $attachableId)
            ->setParameter('type', $attachableType)
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
        return $this->getBaseQueryBuilder()
            ->where('a.target.attachableType = :type')
            ->andWhere('a.target.attachableId = :id')
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
        return $this->getBaseQueryBuilder()
            ->where('a.target.attachableType = :type')
            ->andWhere('a.target.attachableId = :id')
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
            ->andWhere('a.target.attachableType = :type')
            ->andWhere('a.target.attachableId = :id')
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
     * @throws \Exception
     */
    public function getAttachmentsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $results = $this->getPaginatedResults($queryBuilder, $page, $perPage);

        $this->preloadTargets($results['items']);

        return $results;
    }

    /**
     * @param int $attachmentId
     * @return Attachment|null
     */
    public function find(int $attachmentId): ?Attachment
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('m.id = :id')
            ->setParameter('id', $attachmentId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Attachment[]
     */
    public function findAll(): array
    {
        return $this->getBaseQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Attachment[]
     */
    public function findAllWithTargets(): array
    {
        $attachments = $this->findAll();

        $this->preloadTargets($attachments);

        return $attachments;
    }

    /**
     * @param string $title
     * @return Attachment[]
     */
    public function findAttachmentsByTitle(string $title): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('a.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $filename
     * @return Attachment[]
     */
    public function findAttachmentsByFilename(string $filename): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('a.filename = :filename')
            ->setParameter('filename', $filename)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTimeImmutable $fileDate
     * @return Attachment[]
     */
    public function findAttachmentsByFileDate(DateTimeImmutable $fileDate): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('a.fileDate = :fileDate')
            ->setParameter('fileDate', $fileDate)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $path
     * @return Attachment[]
     */
    public function findAttachmentsByPath(string $path): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('a.path = :path')
            ->setParameter('path', $path)
            ->getQuery()
            ->getResult();
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
