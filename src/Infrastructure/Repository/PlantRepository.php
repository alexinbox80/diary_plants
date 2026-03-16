<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Plant;
use App\Domain\ValueObject\Price;
use Doctrine\ORM\EntityManagerInterface;

class PlantRepository extends AbstractRepository
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
     * @param Plant[] $plants
     */
    private function loadAttachmentsForPlants(array $plants): void
    {
        if (empty($plants)) return;

        $ids = array_map(fn(Plant $p) => $p->getId(), $plants);

        $attachments = $this->attachmentRepository->findAllByAttachable('plant::class', $ids);

        $grouped = [];
        foreach ($attachments as $attachment) {
            $grouped[$attachment->getTarget()->getAttachableId()][] = $attachment;
        }

        foreach ($plants as $plant) {
            $plant->setLoadedAttachments($grouped[$plant->getId()] ?? []);
        }
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return Plant[]
     */
    public function getPlantsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('p', 'g')
            ->from(Plant::class, 'p')
            ->leftJoin('p.group', 'g')
            ->orderBy('p.updatedAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return Plant[]
     */
    public function getPlantsPaginatedWithAttachments(int $page, int $perPage): array
    {
        $result = $this->getPlantsPaginated($page, $perPage);

        if (!isset($result['items'])) {
            throw new \RuntimeException('Pagination result is missing "items".');
        }

        if (!is_array($result['items'])) {
            throw new \InvalidArgumentException('"items" must be an array.');
        }

        $this->loadAttachmentsForPlants($result['items']);

        return $result;
    }

    /**
     * @return int
     */
    public function getPlantsCount(): int
    {
        $repository = $this->entityManager->getRepository(Plant::class);

        return $repository->count([]);
    }

    /**
     * @param int|null $groupId
     * @return array
     */
    public function getPlantsForForm(?int $groupId = null): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $qb = $queryBuilder->select('p')
            ->from(Plant::class, 'p')
            ->orderBy('p.title', 'ASC');

        if ($groupId !== null) {
            $qb->where('p.group = :groupId')
                ->setParameter('groupId', $groupId);
        }
        return  $qb->getQuery()->getResult();
    }

    /**
     * @param int $plantId
     * @return Plant|null
     */
    public function find(int $plantId): ?Plant
    {
        $repository = $this->entityManager->getRepository(Plant::class);
        /** @var Plant|null $plant */
        $plant = $repository->find($plantId);

        return $plant;
    }

    /**
     * @return Plant[]
     */
    public function findAll(): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        return $queryBuilder
            ->select('p', 'g')
            ->from(Plant::class, 'p')
            ->leftJoin('p.group', 'g')
            ->orderBy('p.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Plant[]
     */
    public function findAllWithAttachments(): array
    {
        $plants = $this->findAll();

        $this->loadAttachmentsForPlants($plants);

        return $plants;
    }

    /**
     * @param string $title
     * @return Plant[]
     */
    public function findPlantsByTitle(string $title): array
    {
        return $this->entityManager->getRepository(Plant::class)->findBy(['title' => $title]);
    }

    /**
     * @param Price $price
     * @return Plant[]
     */
    public function findPlantsByPrice(Price $price): array
    {
        return $this->entityManager->getRepository(Plant::class)->findBy(['price' => $price]);
    }

    /**
     * @param Plant $plant
     * @return int
     */
    public function create(Plant $plant): int
    {
        return $this->store($plant);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Plant $plant
     * @return void
     */
    public function remove(Plant $plant): void
    {
        $plant->setDeletedAt();
        $this->flush();
    }
}
