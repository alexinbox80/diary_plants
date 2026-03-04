<?php

namespace App\Domain\Service;

use App\Domain\Entity\Status;
use Psr\Cache\InvalidArgumentException;
use App\Domain\Model\Status\StatusModel;
use App\Domain\Model\Status\CreateStatusModel;
use App\Domain\Model\Status\UpdateStatusModel;
use App\Domain\Repository\StatusRepositoryInterface;

class StatusService
{
    public function __construct(
        private readonly GroupService $groupService,
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
        $group = $this->groupService->find($createStatusModel->groupId);

        $status = new Status(
            $group,
            $createStatusModel->letter,
            $createStatusModel->color,
            $createStatusModel->description,
            $createStatusModel->colorDescription
        );

        $this->statusRepository->create($status);

        return $this->statusRepository->toModel($status);
    }

    /**
     * @param Status $status
     * @param UpdateStatusModel $updateStatusModel
     * @return StatusModel
     * @throws InvalidArgumentException
     */
    public function update(Status $status, UpdateStatusModel $updateStatusModel): StatusModel
    {
        $group = $this->groupService->find($updateStatusModel->groupId);

        $status->changeFields(
            $group,
            $updateStatusModel->letter,
            $updateStatusModel->color,
            $updateStatusModel->description,
            $updateStatusModel->colorDescription
        );

        $this->statusRepository->update();

        return $this->statusRepository->toModel($status);
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
