<?php

namespace App\Domain\Service;

use DateTimeImmutable;
use InvalidArgumentException;
use App\Domain\Entity\Attachment;
use App\Domain\Model\Attachment\AttachmentModel;
use App\Domain\Model\Attachment\CreateAttachmentModel;
use App\Domain\Model\Attachment\UpdateAttachmentModel;
use App\Domain\Repository\AttachmentRepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Controller\Web\Dashboard\Image\EditImage\Input\EditImageDTO;
use App\Controller\Web\Dashboard\Image\CreateImage\Input\CreateImageDTO;

class AttachmentService
{
    public function __construct(
        private readonly AttachmentRepositoryInterface $attachmentRepository,
        private readonly ModelFactory $modelFactory,
        private readonly FileService $fileService,
    ) {
    }

    /**
     * @param int $attachmentId
     * @return ?Attachment
     */
    public function find(int $attachmentId): ?Attachment
    {
        return $this->attachmentRepository->find($attachmentId);
    }

    /**
     * @return AttachmentModel[]
     */
    public function findAll(): array
    {
        return $this->attachmentRepository->findAll();
    }

    /**
     * @param string $title
     * @return AttachmentModel[]
     */
    public function findAttachmentsByTitle(string $title): array
    {
        return $this->attachmentRepository->findAttachmentsByTitle($title);
    }

    /**
     * @param string $filename
     * @return AttachmentModel[]
     */
    public function findAttachmentsByFilename(string $filename): array
    {
        return $this->attachmentRepository->findAttachmentsByFilename($filename);
    }

    /**
     * @param DateTimeImmutable $fileDate
     * @return AttachmentModel[]
     */
    public function findAttachmentsByFileDate(DateTimeImmutable $fileDate): array
    {
        return $this->attachmentRepository->findAttachmentsByFileDate($fileDate);
    }

    /**
     * @param string $path
     * @return AttachmentModel[]
     */
    public function findAttachmentsByPath(string $path): array
    {
        return $this->attachmentRepository->findAttachmentsByPath($path);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws InvalidArgumentException
 */
    public function getAttachmentsPaginated(int $page, int $perPage): array
    {
        return $this->attachmentRepository->getAttachmentsPaginated($page, $perPage);
    }

    /**
     * @param CreateAttachmentModel $createAttachmentModel
     * @return AttachmentModel
     * @throws InvalidArgumentException
     */
    public function create(CreateAttachmentModel $createAttachmentModel): AttachmentModel
    {
        $attachment = new Attachment(
            $createAttachmentModel->filename,
            $createAttachmentModel->path,
            $createAttachmentModel->mimeType,
            $createAttachmentModel->alt,
            $createAttachmentModel->title,
            $createAttachmentModel->fileDate,
            $createAttachmentModel->description,
            $createAttachmentModel->attachableId,
            $createAttachmentModel->attachableType
        );

        $this->attachmentRepository->create($attachment);

        return $this->attachmentRepository->toModel($attachment);
    }

    /**
     * @param CreateImageDTO $dto
     * @return AttachmentModel
     */
    public function createFromCreateImageDTO(CreateImageDTO $dto): AttachmentModel
    {
        $this->processFileForDTO($dto);

        $model = $this->modelFactory->makeModel(
            CreateAttachmentModel::class,
            $dto->filename,
            $dto->path,
            $dto->mimeType,
            $dto->alt,
            $dto->title,
            $dto->fileDate,
            $dto->attachableId,
            $dto->attachableType,
            $dto->description,
        );

        return $this->create($model);
    }

    /**
     * @param Attachment $attachment
     * @param UpdateAttachmentModel $updateAttachmentModel
     * @return AttachmentModel
     * @throws InvalidArgumentException
     */
    public function update(Attachment $attachment, UpdateAttachmentModel $updateAttachmentModel): AttachmentModel
    {
        $attachment->changeFields(
            $updateAttachmentModel->filename,
            $updateAttachmentModel->path,
            $updateAttachmentModel->mimeType,
            $updateAttachmentModel->alt,
            $updateAttachmentModel->title,
            $updateAttachmentModel->fileDate,
            $updateAttachmentModel->description,
            $updateAttachmentModel->attachableId,
            $updateAttachmentModel->attachableType,
        );

        $this->attachmentRepository->update();

        return $this->attachmentRepository->toModel($attachment);
    }

    /**
     * @param Attachment $attachment
     * @param EditImageDTO $dto
     * @return void
     */
    public function updateFromEditImageDTO(Attachment $attachment, EditImageDTO $dto): void
    {
        // Если загружен новый файл — обновляем путь и удаляем старый
        if ($dto->imageFile instanceof UploadedFile) {
            $this->removeOldFile($attachment);
            $this->processFileForDTO($dto);
        }

        // Создаём модель обновления
        $model = $this->modelFactory->makeModel(
            UpdateAttachmentModel::class,
            $dto->filename,
            $dto->path,
            $dto->mimeType,
            $dto->alt,
            $dto->title,
            $dto->fileDate,
            $dto->attachableId,
            $dto->attachableType,
            $dto->description,
        );

        // Выполняем обновление
        $this->update($attachment, $model);
    }

    /**
     * @param int $attachmentId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $attachmentId): void
    {
        $attachment = $this->attachmentRepository->find($attachmentId);
        if ($attachment !== null) {
            $this->removeAttachment($attachment);
        }
    }

    /**
     * @param Attachment $attachment
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeAttachment(Attachment $attachment): void
    {
        $this->attachmentRepository->remove($attachment);
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteWithFile(int $id): void
    {
        $attachment = $this->find($id);

        if (!$attachment) {
            throw new \InvalidArgumentException("Вложение с ID {$id} не найдено");
        }

        // Удаляем файл
        $this->removeOldFile($attachment);

        // Удаляем сущность
        $this->removeAttachment($attachment);
    }

    /**
     * Вспомогательные методы
     */
    /**
     * @param CreateImageDTO|EditImageDTO $dto
     * @return void
     */
    private function processFileForDTO(CreateImageDTO|EditImageDTO $dto): void
    {
        if ($dto->imageFile instanceof UploadedFile) {
            $path = $this->fileService->getAttachmentsPath($dto->attachableType, $dto->attachableId);
            $dto->mimeType = $dto->imageFile->getMimeType();

            $uploadedFile = $this->fileService->storeUploadedFile($dto->imageFile, $path);

            $dto->filename = $uploadedFile->getFilename();
            $dto->path = $path;
        }
    }

    /**
     * @param Attachment $attachment
     * @return void
     */
    private function removeOldFile(Attachment $attachment): void
    {
        if ($attachment->getPath() && $attachment->getFilename()) {
            $this->fileService->removeUploadedFile($attachment->getPath() . $attachment->getFilename());
        }
    }
}
