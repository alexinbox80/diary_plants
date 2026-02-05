<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Plant;
use App\Domain\Entity\Attachment;
use App\Domain\ValueObject\Price;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Model\Attachment\AttachmentModel;
use App\Domain\Repository\PlantRepositoryInterface;

class PlantRepositoryDecorator implements PlantRepositoryInterface
{
    public function __construct(
        private readonly PlantRepository $plantRepository,
        private readonly AttachmentRepository $attachmentRepository,
    ) {
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @return array{plantsModel: plantModel[], pagination: array}
     */
    public function getPlantsPaginated(int $page, int $perPage): array
    {
        $plantsPaginated = $this->plantRepository->getPlantsPaginated($page, $perPage);

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

        return $this->toModel($plant);
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

        if ($addRelations) {
            $attachments = $this->attachmentRepository->findByAttachable('plant::class', $plant->getId());

            $attachmentModels = array_map(
                fn (Attachment $attachment): AttachmentModel => AttachmentRepositoryDecorator::makeAttachmentModel($attachment),
                $attachments
            );
        }

        return self::makePlantModel($plant, $attachmentModels);
    }

    /**
     * @param Plant $plant
     * @param array $attachmentModels
     * @return PlantModel
     */
    static function makePlantModel(Plant $plant, array $attachmentModels = []): PlantModel
    {
        return new PlantModel(
            $plant->getId(),
            $plant->getOid(),
            $plant->getTitle(),
            $plant->getRoom(),
            $plant->isShown(),
            $attachmentModels,
            $plant->getDescription(),
            $plant->getQrCodeBase64(),
            $plant->getPurchaseDate(),
            $plant->getVaccinationDate(),
            $plant->getPlantingDate(),
            $plant->getSeller(),
            $plant->getNursery(),
            $plant->getPrice(),
            $plant->getShippingCost(),
            $plant->getPackagingCost(),
            $plant->getSoil(),
            $plant->getComment(),
            $plant->getCreatedAt(),
            $plant->getUpdatedAt()
        );
    }
}
