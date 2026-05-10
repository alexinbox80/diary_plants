<?php

namespace App\Infrastructure\Repository;

use Exception;
use InvalidArgumentException;
use App\Domain\Entity\Offspring;
use App\Domain\Entity\Attachment;
use App\Domain\Model\Offspring\OffspringModel;
use App\Domain\Model\Attachment\AttachmentModel;
use App\Domain\Repository\GroupRepositoryInterface;
use App\Domain\Repository\PlantRepositoryInterface;
use App\Domain\Repository\OffspringRepositoryInterface;

class OffspringRepositoryDecorator implements OffspringRepositoryInterface
{
    public function __construct(
        private readonly OffspringRepository  $offspringRepository,
        private readonly PlantRepositoryInterface $plantRepository,
        private readonly GroupRepositoryInterface $groupRepository,
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return OffspringModel[]
     * @throws Exception
     */
    public function getOffspringsPaginated(int $page, int $perPage): array
    {
        $offspringsPaginated = $this->offspringRepository->getOffspringsPaginatedWithAttachments($page, $perPage);

        if (!is_array($offspringsPaginated['items'])) {
            throw new InvalidArgumentException('Expected array for plants');
        }

        $offspringsModel = array_map(
            fn (Offspring $offspring): OffspringModel => $this->toModel($offspring, true),
            $offspringsPaginated['items']
        );

        return [
            'offspringsModel' => $offspringsModel,
            'pagination' => $offspringsPaginated['pagination']
        ];
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param int|null $groupId
     * @return array{offspringesModel: offspringModel[], pagination: array}
     * @throws Exception
     */
    public function getOffspringsPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array
    {
        $offspringsPaginated = $this->offspringRepository->getOffspringsPaginatedByGroupIdWithAttachments($page, $perPage, $groupId);

        if (!is_array($offspringsPaginated['items'])) {
            throw new InvalidArgumentException('Expected array for offsprings');
        }

        $offspringsModel = array_map(
            fn (Offspring $offspring): OffspringModel => $this->toModel($offspring, true),
            $offspringsPaginated['items']
        );

        return [
            'offspringsModel' => $offspringsModel,
            'pagination' => $offspringsPaginated['pagination']
        ];
    }

    /**
     * @param int $offspringId
     * @return Offspring|null
     */
    public function find(int $offspringId): ?Offspring
    {
        return $this->offspringRepository->find($offspringId);
    }

    /**
     * @param int $offspringId
     * @return OffspringModel|null
     */
    public function findModel(int $offspringId): ?OffspringModel
    {
        $offspring = $this->offspringRepository->find($offspringId);

        return $this->toModel($offspring);
    }

    /**
     * @return OffspringModel[]
     */
    public function findAll(): array
    {
        $offsprings = $this->offspringRepository->findAll();

        return array_map(
            fn (Offspring $offspring): OffspringModel => $this->toModel($offspring, true),
            $offsprings
        );
    }

    /**
     * @param int|null $groupId
     * @return OffspringModel[]
     */
    public function findAllWithAttachments(?int $groupId = null): array
    {
        $offsprings = $this->offspringRepository->findAllWithAttachments($groupId);

        return array_map(
            fn (Offspring $offspring): OffspringModel => $this->toModel($offspring, true),
            $offsprings
        );
    }

    /**
     * @param string $mass
     * @return OffspringModel[]
     */
    public function findOffspringsByMass(string $mass): array
    {
        $offsprings = $this->offspringRepository->findOffspringsByMass($mass);

        return array_map(
            fn (Offspring $offspring): OffspringModel => $this->toModel($offspring),
            $offsprings
        );
    }

    /**
     * @param string $flavor
     * @return OffspringModel[]
     */
    public function findOffspringsByFlavor(string $flavor): array
    {
        $offsprings = $this->offspringRepository->findOffspringsByFlavor($flavor);

        return array_map(
            fn (Offspring $offspring): OffspringModel => $this->toModel($offspring),
            $offsprings
        );
    }

    /**
     * @param string $color
     * @return OffspringModel[]
     */
    public function findOffspringsByColor(string $color): array
    {
        $offsprings = $this->offspringRepository->findOffspringsByColor($color);

        return array_map(
            fn (Offspring $offspring): OffspringModel => $this->toModel($offspring),
            $offsprings
        );
    }

    /**
     * @param Offspring $offspring
     * @return int
     */
    public function create(Offspring $offspring): int
    {
        return $this->offspringRepository->create($offspring);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->offspringRepository->update();
    }

    /**
     * @param Offspring $offspring
     * @return void
     */
    public function remove(Offspring $offspring): void
    {
        $this->offspringRepository->remove($offspring);
    }

    /**
     * @param Offspring $offspring
     * @param bool $addRelations
     * @return OffspringModel
     */
    public function toModel(Offspring $offspring, bool $addRelations = false): OffspringModel
    {
        $attachmentModels = [];
        $plantModel = null;
        $groupModel = null;

        if ($addRelations) {
            if ($offspring->getLoadedAttachments()) {
                $attachmentModels = array_map(
                    fn (Attachment $attachment): AttachmentModel => AttachmentModel::fromEntity($attachment),
                    $offspring->getLoadedAttachments()
                );
            }

            $plantModel = $this->plantRepository->toModel($offspring->getPlant());
            $groupModel = $this->groupRepository->toModel($offspring->getGroup());
        }

        return OffspringModel::fromEntity($offspring, $attachmentModels, $plantModel, $groupModel);
    }
}
