<?php

namespace App\Domain\Service;

use App\Domain\Entity\Analytic;
use App\Domain\Entity\Plant;
use App\Domain\ValueObject\OId;
use App\Domain\ValueObject\Price;
use App\Domain\Model\Plant\PlantModel;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\ValueObject\Plant\LifeCycle;
use App\Domain\ValueObject\Plant\SalesInfo;
use App\Domain\Model\Plant\CreatePlantModel;
use App\Domain\Model\Plant\UpdatePlantModel;
use App\Domain\ValueObject\Plant\PurchaseInfo;
use App\Domain\ValueObject\Plant\PlantIdentifier;
use App\Domain\Repository\PlantRepositoryInterface;
use App\Domain\Repository\AnalyticRepositoryInterface;
use App\Domain\Repository\AttachmentRepositoryInterface;
use App\Domain\ValueObject\Enum\Attachment\AttachableType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Controller\Web\Dashboard\Plant\EditPlant\Input\EditPlantDTO;
use App\Controller\Web\Dashboard\Plant\CreatePlant\Input\CreatePlantDTO;

class PlantService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly string $webURL,
        private readonly PlantRepositoryInterface $plantRepository,
        private readonly ModelFactory $modelFactory,
        private readonly GroupService $groupService,
        private readonly FileService $fileService,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly AttachmentRepositoryInterface $attachmentRepository,
        private readonly AnalyticRepositoryInterface $analyticRepository,
    ) {
    }

    public function getPlantsForDiary(int $groupID): array
    {
        return $this->plantRepository->getPlantsForDiary($groupID);
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
        return $this->plantRepository->findAllWithAttachments();
    }

    /**
     * @return PlantModel[]
     */
    public function findAllByGroupId(?int $groupId = null): array
    {
        return $this->plantRepository->findAllWithAttachments($groupId);
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
     * @param OId $oid
     * @return PlantModel|null
     */
    public function findPlantByUUID(Oid $oid): ?PlantModel
    {
        return $this->plantRepository->findPlantByUUID($oid);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return PlantModel[]
     */
    public function getPlantsPaginated(int $page, int $perPage): array
    {
        return $this->plantRepository->getPlantsPaginated($page, $perPage);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param int|null $groupId
     * @return PlantModel[]
     */
    public function getPlantsPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array
    {
        return $this->plantRepository->getPlantsPaginatedByGroupId($page, $perPage, $groupId);
    }

    public function getPlantsCount(): int
    {
        return $this->plantRepository->getPlantsCount();
    }

    /**
     * @param CreatePlantModel $createPlantModel
     * @return PlantModel
     */
    public function create(CreatePlantModel $createPlantModel): PlantModel
    {
        return $this->entityManager->wrapInTransaction(function() use ($createPlantModel) {
            $group = $this->groupService->find($createPlantModel->groupId);

            $plant = new Plant(
                $group,
                $createPlantModel->title,
                $createPlantModel->room
            );

            $plant->changeLifeCycle(new LifeCycle(
                $createPlantModel->plantingDate,
                $createPlantModel->vaccinationDate,
                $createPlantModel->soil,
            ))->changePurchaseInfo(new PurchaseInfo(
                $createPlantModel->price,
                $createPlantModel->shippingCost,
                $createPlantModel->packagingCost,
                $createPlantModel->seller,
                $createPlantModel->nursery,
                $createPlantModel->purchaseDate
            ))->changeSalesInfo(new SalesInfo(
                $createPlantModel->sellingDate,
                $createPlantModel->sellingPrice,
                $createPlantModel->isSold
            ))->setComment($createPlantModel->comment)
                ->setDescription($createPlantModel->description);

            $plant->show();

            $this->plantRepository->create($plant);

            $this->processFileForQrCode($plant);

            $analytic = new Analytic(
                $plant,
                $group
            );
            $this->analyticRepository->create($analytic);

            return $this->plantRepository->toModel($plant);
        });
    }

    /**
     * @param CreatePlantDTO $dto
     * @return PlantModel
     */
    public function createFromCreatePlantDTO(CreatePlantDTO $dto): PlantModel
    {
        $model = $this->modelFactory->makeModel(
            CreatePlantModel::class,
            $dto->groupId,
            $dto->title,
            $dto->room,
            $dto->isShown,
            $dto->description,
            $dto->purchaseDate,
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
     */
    public function update(Plant $plant, UpdatePlantModel $updatePlantModel): PlantModel
    {
        $group = $this->groupService->find($updatePlantModel->groupId);

        $attachments = $this->attachmentRepository->findEntitiesByAttachable(AttachableType::PLANT->value, $plant->getId());
        $plant->setLoadedAttachments($attachments);

        $plant->moveToGroup($group)
            ->setTitle($updatePlantModel->title)
            ->setRoom($updatePlantModel->room)
            ->changeLifeCycle(new LifeCycle(
                $updatePlantModel->plantingDate,
                $updatePlantModel->vaccinationDate,
                $updatePlantModel->soil,
            ))->changePurchaseInfo(new PurchaseInfo(
                $updatePlantModel->price,
                $updatePlantModel->shippingCost,
                $updatePlantModel->packagingCost,
                $updatePlantModel->seller,
                $updatePlantModel->nursery,
                $updatePlantModel->purchaseDate
            ))->changeSalesInfo(new SalesInfo(
                $updatePlantModel->sellingDate,
                $updatePlantModel->sellingPrice,
                $updatePlantModel->isSold
            ))->setComment($updatePlantModel->comment)
            ->setDescription($updatePlantModel->description);

        if ($updatePlantModel->isShown)
            $plant->show();
        else
            $plant->hide();

        $oldPath = $plant->getPlantIdentifier()->getQrCodeLink();

        // Вызываем сервис, который переименует папку/файл на диске (переносим в новый каталог groupId)
        $newPath = $this->fileService->moveAttachmentQrFiles($oldPath, $updatePlantModel->groupId);
        $plant->changePlantIdentifier(
            new PlantIdentifier(
                $plant->getPlantIdentifier()->getOid(),
                $newPath
            )
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
            $dto->groupId,
            $dto->title,
            $dto->room,
            $dto->isShown,
            $dto->description,
            $dto->purchaseDate,
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
     */
    public function removePlant(Plant $plant): void
    {
        $this->plantRepository->remove($plant);
    }

    /**
     * @param Plant $plant
     * @return void
     */
    public function deleteWithQrCode(Plant $plant): void
    {
        //$plant = $this->find($id);

//        if (!$plant) {
//            throw new \InvalidArgumentException("Plant with ID {$id} not found");
//        }

        // Удаляем файл
        $this->removeOldQrCodeFile($plant);

        // Удаляем сущность
        $this->removePlant($plant);
    }

    /**
     * Вспомогательные методы
     * @param Plant $plant
     * @return void
     */
    private function processFileForQrCode(Plant $plant): void
    {
        $path = $this->fileService->getAttachmentsPath('qr-code::class', $plant->getGroup()->getId());

        $uuid = $plant->getPlantIdentifier()->getOid()->toString();
        $url = $this->webURL . $this->urlGenerator->generate('dashboard.plants.profile', ['uuid' => $uuid]);
        $link = $this->fileService->getQrCodeLink($path . $plant->getId() . '/', $uuid, $url);

        $plant->changePlantIdentifier(
            $plant->getPlantIdentifier()->withQrCodeLink($link)
        );

        //иначе не сохраняет qr code link
        $this->plantRepository->update();
    }

    /**
     * @param Plant $plant
     * @return void
     */
    private function removeOldQrCodeFile(Plant $plant): void
    {
        if ($plant->getPlantIdentifier()->getQrCodeLink()) {
            $this->fileService->removeUploadedFile($plant->getPlantIdentifier()->getQrCodeLink());
        }
    }
}
