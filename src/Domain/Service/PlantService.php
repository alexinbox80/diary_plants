<?php

namespace App\Domain\Service;

use App\Domain\Entity\Plant;
use App\Domain\ValueObject\Price;
use App\Domain\Model\Plant\PlantModel;
use Psr\Cache\InvalidArgumentException;
use Endroid\QrCode\ErrorCorrectionLevel;
use App\Domain\Model\Plant\CreatePlantModel;
use App\Domain\Model\Plant\UpdatePlantModel;
use Endroid\QrCode\Builder\BuilderInterface;
use App\Domain\Repository\PlantRepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Controller\Web\Dashboard\Plant\EditPlant\Input\EditPlantDTO;
use App\Controller\Web\Dashboard\Plant\CreatePlant\Input\CreatePlantDTO;

class PlantService
{
    public function __construct(
        private readonly PlantRepositoryInterface $plantRepository,
        private readonly ModelFactory $modelFactory,
        private readonly GroupService $groupService,
        private readonly FileService $fileService,
        private readonly BuilderInterface $customQrCodeBuilder
    ) {
    }

    /**
     * @param int|null $groupId
     * @return PlantModel[]
     */
    public function getChoicesForChoiceType(?int $groupId = null): array
    {
        // Получаем список выбора: [title => id]
        $choices = [];
        $plantModels = $this->plantRepository->getPlantsForForm($groupId);
        foreach ($plantModels as $plant) {
            $choices[$plant->getTitle()] = $plant->getId();
        }

        return $choices;
    }

    /**
     * @param int $plantId
     * @return ?Plant
     */
    public function find(int $plantId): ?Plant
    {
        return $this->plantRepository->find($plantId);
    }

    /**
     * @param int $plantId
     * @return ?PlantModel
     */
    public function findModel(int $plantId): ?PlantModel
    {
        return $this->plantRepository->findModel($plantId);
    }

    /**
     * @return PlantModel[]
     */
    public function findAll(): array
    {
        return $this->plantRepository->findAll();
    }

    /**
     * @param string $title
     * @return PlantModel[]
     */
    public function findPlantsByTitle(string $title): array
    {
        return $this->plantRepository->findPlantsByTitle($title);
    }

    /**
     * @param Price $price
     * @return PlantModel[]
     */
    public function findPlantsByPrice(Price $price): array
    {
        return $this->plantRepository->findPlantsByPrice($price);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws InvalidArgumentException
     */
    public function getPlantsPaginated(int $page, int $perPage): array
    {
        return $this->plantRepository->getPlantsPaginated($page, $perPage);
    }

    public function getPlantsCount(): int
    {
        return $this->plantRepository->getPlantsCount();
    }

    /**
     * @param CreatePlantModel $createPlantModel
     * @return PlantModel
     * @throws InvalidArgumentException
     */
    public function create(CreatePlantModel $createPlantModel): PlantModel
    {
        $group = $this->groupService->find($createPlantModel->groupId);

        $plant = new Plant(
            $group,
            $createPlantModel->title,
            $createPlantModel->room,
            $createPlantModel->isShown,
            $createPlantModel->description,
            $createPlantModel->purchaseDate,
            $createPlantModel->vaccinationDate,
            $createPlantModel->plantingDate,
            $createPlantModel->seller,
            $createPlantModel->nursery,
            $createPlantModel->price,
            $createPlantModel->shippingCost,
            $createPlantModel->packagingCost,
            $createPlantModel->soil,
            $createPlantModel->isSold,
            $createPlantModel->sellingDate,
            $createPlantModel->sellingPrice,
            $createPlantModel->comment
        );

        $this->plantRepository->create($plant);

        $this->processFileForQrCode($plant);

        return $this->plantRepository->toModel($plant);
    }

    /**
     * @param CreatePlantDTO $dto
     * @return PlantModel
     */
    public function createFromCreatePlantDTO(CreatePlantDTO $dto): PlantModel
    {
        $model = $this->modelFactory->makeModel(
            CreatePlantModel::class,
            2,
            $dto->title,
            $dto->room,
            $dto->isShown,
            $dto->description,
            $dto->plantingDate,
            $dto->vaccinationDate,
            $dto->plantingDate,
            $dto->seller,
            $dto->nursery,
            $dto->price !== null ? Price::fromString($dto->price) : null,
            $dto->shippingCost !== null ? Price::fromString($dto->shippingCost) : null,
            $dto->packagingCost !== null ? Price::fromString($dto->packagingCost) : null,
            $dto->soil,
            $dto->isSold,
            $dto->sellingDate,
            $dto->sellingPrice !== null ? Price::fromString($dto->sellingPrice) : null,
            $dto->comment
        );

        return $this->create($model);
    }

    /**
     * @param Plant $plant
     * @param UpdatePlantModel $updatePlantModel
     * @return PlantModel
     * @throws InvalidArgumentException
     */
    public function update(Plant $plant, UpdatePlantModel $updatePlantModel): PlantModel
    {
        $group = $this->groupService->find($updatePlantModel->groupId);

        $plant->changeFields(
            $group,
            $updatePlantModel->title,
            $updatePlantModel->room,
            $updatePlantModel->isShown,
            $updatePlantModel->description,
            $updatePlantModel->purchaseDate,
            $updatePlantModel->vaccinationDate,
            $updatePlantModel->plantingDate,
            $updatePlantModel->seller,
            $updatePlantModel->nursery,
            $updatePlantModel->price,
            $updatePlantModel->shippingCost,
            $updatePlantModel->packagingCost,
            $updatePlantModel->soil,
            $updatePlantModel->isSold,
            $updatePlantModel->sellingDate,
            $updatePlantModel->sellingPrice,
            $updatePlantModel->comment
        );

        $this->plantRepository->update();

        return $this->plantRepository->toModel($plant);
    }

    /**
     * @param Plant $plant
     * @param EditPlantDTO $dto
     * @return void
     */
    public function updateFromEditPlantDTO(Plant $plant, EditPlantDTO $dto): void
    {
        // Создаём модель обновления
        $model = $this->modelFactory->makeModel(
            UpdatePlantModel::class,
            2,
            $dto->title,
            $dto->room,
            $dto->isShown,
            $dto->description,
            $dto->plantingDate,
            $dto->vaccinationDate,
            $dto->plantingDate,
            $dto->seller,
            $dto->nursery,
            $dto->price !== null ? Price::fromString($dto->price) : null,
            $dto->shippingCost !== null ? Price::fromString($dto->shippingCost) : null,
            $dto->packagingCost !== null ? Price::fromString($dto->packagingCost) : null,
            $dto->soil,
            $dto->isSold,
            $dto->sellingDate,
            $dto->sellingPrice !== null ? Price::fromString($dto->sellingPrice) : null,
            $dto->comment
        );

        // Выполняем обновление
        $this->update($plant, $model);
    }

    /**
     * @param int $plantId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $plantId): void
    {
        $plant = $this->plantRepository->find($plantId);
        if ($plant !== null) {
            $this->plantRepository->remove($plant);
        }
    }

    /**
     * @param Plant $plant
     * @return void
     * @throws InvalidArgumentException
     */
    public function removePlant(Plant $plant): void
    {
        $this->plantRepository->remove($plant);
    }

    /**
     * Вспомогательные методы
     *
     */
    private function processFileForQrCode(Plant $plant): void
    {
        $path = $this->fileService->getAttachmentsPath('qr-code::class', $plant->getId());

        $uuid = $plant->getOid();
        $fileName = $uuid . '.png';
        $fullPath = $path . $fileName;

        // Используем билдер пакета
        $result = $this->customQrCodeBuilder->build(
            writer: new \Endroid\QrCode\Writer\PngWriter(),
            data: $uuid,
            encoding: new \Endroid\QrCode\Encoding\Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: 300,
            margin: 10,
        );

        // Создаём директорию, если не существует
        if (!is_dir('storage/' . $path)) {
            mkdir('storage/' . $path, 0755, true);
        }

        // Сохраняем файл
        $result->saveToFile('storage/' . $fullPath);

        // Создаём объект UploadedFile
//        $mimeType = mime_content_type($fullPath);
//
//        $uploadedFile = new UploadedFile(
//            $fullPath,
//            $fileName,
//            $mimeType,
//            null,
//            true // помечаем как "тестовый", чтобы не проверять UPLOAD_ERR_OK
//        );
//
//        $uploadedFile = $this->fileService->storeUploadedFile($uploadedFile, $path);

        $plant->setQrCodeLink($path . $fileName);
    }

    /**
     * @param Plant $plant
     * @return void
     */
    private function removeOldQrCodeFile(Plant $plant): void
    {
        if ($plant->getQrCodeLink()) {
            $this->fileService->removeUploadedFile($plant->getQrCodeLink());
        }
    }
}
