<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Status;
use App\Domain\Model\Status\StatusModel;
use App\Domain\Repository\StatusRepositoryInterface;

class StatusRepositoryDecorator implements StatusRepositoryInterface
{
    public function __construct(
        private readonly StatusRepository $statusRepository,
    ) {
    }

    /**
     * @return StatusModel[]
     */
    public function getStatusesPaginated(int $page, int $perPage): array
    {
        $statuses = $this->statusRepository->getStatusesPaginated($page, $perPage);

        return array_map(
            static fn (Status $status): StatusModel => new StatusModel(
                $status->getId(),
                $status->getLetter(),
                $status->getColor(),
                $status->getDescription(),
                $status->getColorDescription(),
                $status->getCreatedAt(),
                $status->getUpdatedAt()
            ),
            $statuses
        );
    }

    /**
     * @param int $statusId
     * @return StatusModel|null
     */
    public function find(int $statusId): ?StatusModel
    {
        $status = $this->statusRepository->find($statusId);

        return new StatusModel(
            $status->getId(),
            $status->getLetter(),
            $status->getColor(),
            $status->getDescription(),
            $status->getColorDescription(),
            $status->getCreatedAt(),
            $status->getUpdatedAt()
        );
    }

    /**
     * @return StatusModel[]
     */
    public function findAll(): array
    {
        $statuses = $this->statusRepository->findAll();

        return array_map(
            static fn (Status $status): StatusModel => new StatusModel(
                $status->getId(),
                $status->getLetter(),
                $status->getColor(),
                $status->getDescription(),
                $status->getColorDescription(),
                $status->getCreatedAt(),
                $status->getUpdatedAt()
            ),
            $statuses
        );
    }

    /**
     * @param string $letter
     * @return StatusModel[]
     */
    public function findStatusesByLetter(string $letter): array
    {
        $statuses = $this->statusRepository->findStatusesByLetter($letter);

        return array_map(
            static fn (Status $status): StatusModel => new StatusModel(
                $status->getId(),
                $status->getLetter(),
                $status->getColor(),
                $status->getDescription(),
                $status->getColorDescription(),
                $status->getCreatedAt(),
                $status->getUpdatedAt()
            ),
            $statuses
        );
    }

    /**
     * @param string $color
     * @return StatusModel[]
     */
    public function findStatusesByColor(string $color): array
    {
        $statuses = $this->statusRepository->findStatusesByColor($color);

        return array_map(
            static fn (Status $status): StatusModel => new StatusModel(
                $status->getId(),
                $status->getLetter(),
                $status->getColor(),
                $status->getDescription(),
                $status->getColorDescription(),
                $status->getCreatedAt(),
                $status->getUpdatedAt()
            ),
            $statuses
        );
    }

    /**
     * @param Status $status
     * @return int
     */
    public function create(Status $status): int
    {
        return $this->statusRepository->create($status);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->statusRepository->update();
    }

    /**
     * @param Status $status
     * @return void
     */
    public function remove(Status $status): void
    {
        $this->statusRepository->remove($status);
    }
}
