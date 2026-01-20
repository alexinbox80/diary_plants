<?php

namespace App\Domain\Service;

use App\Domain\Entity\Attachment;
use App\Domain\Model\Attachment\AttachmentModel;
use App\Domain\Model\Attachment\CreateAttachmentModel;
use App\Domain\Model\Attachment\UpdateAttachmentModel;
use App\Domain\Repository\AttachmentRepositoryInterface;
use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AttachmentService
{
    public function __construct(
        private readonly AttachmentRepositoryInterface $attachmentRepository
    ) {
    }

    public function createAttachment(
        UploadedFile $file,
        string $targetType,
        int $targetId
    ): ?Attachment
    {
        $filename = uniqid() . '-' . $file->getClientOriginalName();
        $file->move('uploads/attachments', $filename);

//        $attachment = new Attachment();
//        $attachment->setFilename($filename);
//        $attachment->setPath('uploads/attachments/' . $filename);
//        $attachment->setTargetType($targetType);
//        $attachment->setTargetId($targetId);

        return null;
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
     * @return AttachmentModel[]
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
            $createAttachmentModel->title,
            $createAttachmentModel->fileDate,
            $createAttachmentModel->description,
            $createAttachmentModel->attachableId,
            $createAttachmentModel->attachableType
        );

        $this->attachmentRepository->create($attachment);

        return new AttachmentModel(
            $attachment->getId(),
            $attachment->getFilename(),
            $attachment->getPath(),
            $attachment->getTitle(),
            $attachment->getFileDate(),
            $attachment->getDescription(),
            $attachment->getAttachableId(),
            $attachment->getAttachableType(),
            $attachment->getCreatedAt(),
            $attachment->getUpdatedAt()
        );
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
            $updateAttachmentModel->title,
            $updateAttachmentModel->fileDate,
            $updateAttachmentModel->description,
            $updateAttachmentModel->attachableId,
            $updateAttachmentModel->attachableType,
        );

        $this->attachmentRepository->update();

        return new AttachmentModel(
            $attachment->getId(),
            $attachment->getFilename(),
            $attachment->getPath(),
            $attachment->getTitle(),
            $attachment->getFileDate(),
            $attachment->getDescription(),
            $attachment->getAttachableId(),
            $attachment->getAttachableType(),
            $attachment->getCreatedAt(),
            $attachment->getUpdatedAt()
        );
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
            $this->attachmentRepository->remove($attachment);
        }
    }

    /**
     * @param Attachment $attachment
     * @return void
     * @throws InvalidArgumentException
     */
    public function removePlant(Attachment $attachment): void
    {
        $this->attachmentRepository->remove($attachment);
    }
}
