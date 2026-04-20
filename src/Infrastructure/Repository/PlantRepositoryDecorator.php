<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Plant;
use App\Domain\Entity\Usage;
use App\Domain\ValueObject\OId;
use App\Domain\Entity\Offspring;
use App\Domain\Entity\Repotting;
use App\Domain\ValueObject\Price;
use App\Domain\Entity\Attachment;
use Doctrine\ORM\PersistentCollection;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Model\Usage\UsageModel;
use App\Domain\Model\Repotting\RepottingModel;
use App\Domain\Model\Offspring\OffspringModel;
use App\Domain\Model\Attachment\AttachmentModel;
use App\Domain\Repository\PlantRepositoryInterface;

class PlantRepositoryDecorator implements PlantRepositoryInterface
{
    public function __construct(
        private readonly GroupRepositoryDecorator $groupRepository,
        private readonly PlantRepository $plantRepository,
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @return array{plantsModel: plantModel[], pagination: array}
     * @throws \Exception
     */
    public function getPlantsPaginated(int $page, int $perPage): array
    {
        $plantsPaginated = $this->plantRepository->getPlantsPaginatedWithAttachments($page, $perPage);

        if (!is_array($plantsPaginated['items'])) {
            throw new \InvalidArgumentException('Expected array for plants');
        }

        $plantsModel = array_map(
            fn (Plant $plant): PlantModel => $this->toModel($plant, true),
            $plantsPaginated['items']
        );

        return [
            'plantsModel' => $plantsModel,
            'pagination' => $plantsPaginated['pagination']
        ];
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param int|null $groupId
     * @return array
     * @return array{plantsModel: plantModel[], pagination: array}
     * @throws \Exception
     */
    public function getPlantsPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array
    {
        $plantsPaginated = $this->plantRepository->getPlantsPaginatedByGroupIdWithAttachments($page, $perPage, $groupId);

        if (!is_array($plantsPaginated['items'])) {
            throw new \InvalidArgumentException('Expected array for plants');
        }

        $plantsModel = array_map(
            fn (Plant $plant): PlantModel => $this->toModel($plant, true),
            $plantsPaginated['items']
        );

        return [
            'plantsModel' => $plantsModel,
            'pagination' => $plantsPaginated['pagination']
        ];
    }

    /**
     * @return int
     */
    public function getPlantsCount(): int
    {
        return $this->plantRepository->getPlantsCount();
    }

    /**
     * @param int|null $groupId
     * @return PlantModel[]
     */
    public function getPlantsForForm(?int $groupId = null): array
    {
        $plants = $this->plantRepository->getPlantsForForm($groupId);

        return array_map(
            fn (Plant $plant): PlantModel => $this->toModel($plant),
            $plants
        );
    }

    /**
     * @param int|null $groupId
     * @param bool|null $isSold
     * @return array
     */
    public function getPlantsForDiary(?int $groupId = null, ?bool $isSold = false): array
    {
        $plants = $this->plantRepository->getPlantsForDairy($groupId, $isSold);

        return array_map(
            fn (Plant $plant): array => [
                'id'=> $plant->getId(),
                'title'=> $plant->getTitle(),
                'oid'=> $plant->getPlantIdentifier()->getOid()->toString(),
            ],
            $plants
        );
    }

    /**
     * @param int $plantId
     * @return Plant|null
     */
    public function find(int $plantId): ?Plant
    {
        return $this->plantRepository->find($plantId);
    }

    /**
     * @param int $plantId
     * @return PlantModel|null
     */
    public function findModel(int $plantId): ?PlantModel
    {
        $plant = $this->plantRepository->find($plantId);

        return $plant ? $this->toModel($plant) : null;
    }

    /**
     * @return PlantModel[]
     */
    public function findAll(): array
    {
        $plants = $this->plantRepository->findAll();

        return array_map(
            fn (Plant $plant): PlantModel => $this->toModel($plant, true),
            $plants
        );
    }

    /**
     * @return PlantModel[]
     */
    public function findAllWithAttachments(?int $groupId = null): array
    {
        $plants = $this->plantRepository->findAllWithAttachments($groupId);

        return array_map(
            fn (Plant $plant): PlantModel => $this->toModel($plant, true),
            $plants
        );
    }

    /**
     * @param string $title
     * @return PlantModel[]
     */
    public function findPlantsByTitle(string $title): array
    {
        $plants = $this->plantRepository->findPlantsByTitle($title);

        return array_map(
            fn (Plant $plant): PlantModel => $this->toModel($plant),
            $plants
        );
    }

    /**
     * @param Price $price
     * @return PlantModel[]
     */
    public function findPlantsByPrice(Price $price): array
    {
        $plants = $this->plantRepository->findPlantsByPrice($price);

        return array_map(
            fn (Plant $plant): PlantModel => $this->toModel($plant),
            $plants
        );
    }

    /**
     * @param Oid $oid
     * @return ?PlantModel
     */
    public function findPlantByUUID(Oid $oid): ?PlantModel
    {
        $plant = $this->plantRepository->findPlantByUUID($oid);

        return $plant ? $this->toModel($plant, true) : null;
    }

    /**
     * @param Plant $plant
     * @return int
     */
    public function create(Plant $plant): int
    {
        return $this->plantRepository->create($plant);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->plantRepository->update();
    }

    /**
     * @param Plant $plant
     * @return void
     */
    public function remove(Plant $plant): void
    {
        $this->plantRepository->remove($plant);
    }

    /**
     * @param Plant $plant
     * @param bool $addRelations
     * @return PlantModel
     */
    public function toModel(Plant $plant, bool $addRelations = false): PlantModel
    {
        $attachmentModels = [];
        $groupModel = null;
        $offspringModels = [];
        $repottingModels = [];
        $usageModels = [];

        if ($addRelations) {
            if ($plant->getLoadedAttachments()) {
                $attachmentModels = array_map(
                    fn (Attachment $attachment): AttachmentModel => AttachmentModel::fromEntity($attachment),
                    $plant->getLoadedAttachments()
                );
            }

            $groupModel = $this->groupRepository->toModel($plant->getGroup());

            $usages = $plant->getUsages();
            if ($usages instanceof PersistentCollection && $usages->isInitialized() && !$usages->isEmpty()) {
                $usageModels = array_map(
                    fn (Usage $usage): UsageModel => UsageModel::fromEntity($usage),
                    $usages->toArray()
                );
            }

            $offsprings = $plant->getOffsprings();
            if ($offsprings instanceof PersistentCollection && $offsprings->isInitialized() && !$offsprings->isEmpty()) {
                $offspringModels = array_map(
                    function (Offspring $offspring): OffspringModel {
                        // 1. Сначала превращаем сущности Attachment в модели AttachmentModel
                        $attachmentModels = array_map(
                            fn (Attachment $attachment): AttachmentModel => AttachmentModel::fromEntity($attachment),
                            $offspring->getLoadedAttachments()
                        );

                        // 2. Передаем полученный массив вторым аргументом в fromEntity
                        return OffspringModel::fromEntity(
                            $offspring,
                            $attachmentModels
                        );
                    },
                    $offsprings->toArray()
                );
            }

            $repottings = $plant->getRepottings();
            if ($repottings instanceof PersistentCollection && $repottings->isInitialized() && !$repottings->isEmpty()) {
                $repottingModels = array_map(
                    fn (Repotting $repotting): RepottingModel => RepottingModel::fromEntity($repotting),
                    $repottings->toArray()
                );
            }
        }

        return PlantModel::fromEntity($plant, $attachmentModels, $groupModel, $usageModels, $offspringModels, $repottingModels);
    }
}
