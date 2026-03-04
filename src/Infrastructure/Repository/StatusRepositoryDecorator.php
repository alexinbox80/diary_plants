<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Status;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Status\StatusModel;
use App\Domain\Repository\StatusRepositoryInterface;

class StatusRepositoryDecorator implements StatusRepositoryInterface
{
    public function __construct(
        private readonly GroupRepositoryDecorator $groupRepository,
        private readonly StatusRepository $statusRepository,
    ) {
    }

    /**
     * @return StatusModel[]
     */
    public function getStatusesPaginated(int $page, int $perPage): array
    {
        $statusesPaginated = $this->statusRepository->getStatusesPaginated($page, $perPage);

        if (!is_array($statusesPaginated['items'])) {
            throw new \InvalidArgumentException('Expected array for statuses');
        }

        $statusesModel = array_map(
            fn (Status $status): StatusModel => $this->toModel($status, true),
            $statusesPaginated['items']
        );

        return [
            'statusesModel' => $statusesModel,
            'pagination' => $statusesPaginated['pagination']
        ];
    }

    /**
     * @param int $statusId
     * @return Status|null
     */
    public function find(int $statusId): ?Status
    {
        return $this->statusRepository->find($statusId);
    }

    /**
     * @param int $statusId
     * @return StatusModel|null
     */
    public function findModel(int $statusId): ?StatusModel
    {
        $status = $this->statusRepository->find($statusId);

        return $this->toModel($status);
    }

    /**
     * @return StatusModel[]
     */
    public function findAll(): array
    {
        $statuses = $this->statusRepository->findAll();

        return array_map(
            fn (Status $status): StatusModel => $this->toModel($status, true),
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
            fn (Status $status): StatusModel => $this->toModel($status),
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
            fn (Status $status): StatusModel => $this->toModel($status),
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

    /**
     * @param Status $status
     * @param bool $addRelations
     * @return StatusModel
     */
    public function toModel(Status $status, bool $addRelations = false): StatusModel
    {
        $groupModel = null;

        if ($addRelations) {
            $groupModel = $this->groupRepository->findModel($status->getGroup()->getId());
        }

        return self::makeStatusModel($status, $groupModel);
    }

    /**
     * @param Status $status
     * @param GroupModel|null $groupModel
     * @return StatusModel
     */
    static function makeStatusModel(Status $status, ?GroupModel $groupModel = null): StatusModel
    {
        return new StatusModel(
            $status->getId(),
            $status->getGroup()->getId(),
            $status->getLetter(),
            $status->getColor(),
            $status->getDescription(),
            $status->getColorDescription(),
            $groupModel,
            $status->getCreatedAt(),
            $status->getUpdatedAt()
        );
    }
}
