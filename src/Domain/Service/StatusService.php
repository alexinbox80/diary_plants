<?php

namespace App\Domain\Service;

use App\Domain\Entity\Status;
use App\Domain\Model\Status\CreateStatusModel;
use App\Domain\Model\Status\UpdateStatusModel;
use App\Domain\Model\Status\StatusModel;
use App\Domain\Repository\StatusRepositoryInterface;
use Psr\Cache\InvalidArgumentException;

class StatusService
{
    public function __construct(
        private readonly StatusRepositoryInterface $statusRepository
    ) {
    }

    /**
     * @param int $statusId
     * @return ?Status
     */
    public function find(int $statusId): ?Status
    {
        return $this->statusRepository->find($statusId);
    }

    /**
     * @return Status[]
     */
    public function findAll(): array
    {
        return $this->statusRepository->findAll();
    }

    /**
     * @param string $letter
     * @return StatusModel[]
     */
    public function findStatusesByLetter(string $letter): array
    {
        return $this->statusRepository->findStatusesByLetter($letter);
    }

    /**
     * @param string $color
     * @return StatusModel[]
     */
    public function findStatusesByColor(string $color): array
    {
        return $this->statusRepository->findStatusesByColor($color);
    }


    /**
     * @return StatusModel[]
     * @throws InvalidArgumentException
     */
    public function getStatusPaginated(int $page, int $perPage): array
    {
        return $this->statusRepository->getStatusesPaginated($page, $perPage);
    }

    /**
     * @param CreateStatusModel $createStatusModel
     * @return StatusModel
     * @throws InvalidArgumentException
     */
    public function create(CreateStatusModel $createStatusModel): StatusModel
    {
        $status = new Status(
            $createStatusModel->letter,
            $createStatusModel->color,
            $createStatusModel->description,
            $createStatusModel->colorDescription
        );

        $this->statusRepository->create($status);

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
     * @param Status $status
     * @param UpdateStatusModel $updateStatusModel
     * @return StatusModel
     * @throws InvalidArgumentException
     */
    public function update(Status $status, UpdateStatusModel $updateStatusModel): StatusModel
    {
        $status->changeFields(
            $updateStatusModel->letter,
            $updateStatusModel->color,
            $updateStatusModel->description,
            $updateStatusModel->colorDescription
        );

        $this->statusRepository->update();

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
     * @param int $statusId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $statusId): void
    {
        $status = $this->statusRepository->find($statusId);
        if ($status !== null) {
            $this->statusRepository->remove($status);
        }
    }

    /**
     * @param Status $status
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeStatus(Status $status): void
    {
        $this->statusRepository->remove($status);
    }
}
