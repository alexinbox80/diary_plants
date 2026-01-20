<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Attachment;
use App\Domain\Model\Attachment\AttachmentModel;
use App\Domain\Repository\AttachmentRepositoryInterface;
use DateTimeImmutable;

class AttachmentRepositoryDecorator implements AttachmentRepositoryInterface
{
    public function __construct(
        private readonly AttachmentRepository $attachmentRepository,
    ) {
    }

    /**
     * @return AttachmentModel[]
     */
    public function findByAttachable(string $attachableType, int $attachableId): array
    {
        $attachments = $this->attachmentRepository->findByAttachable($attachableType, $attachableId);

        return array_map(
            static fn (Attachment $attachment): AttachmentModel => new AttachmentModel(
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
            ),
            $attachments
        );
    }


    /**
     * @return AttachmentModel[]
     */
    public function findByAttachableWithDeleted(string $attachableType, int $attachableId): array
    {
        $attachments = $this->attachmentRepository->findByAttachableWithDeleted($attachableType, $attachableId);

        return array_map(
            static fn (Attachment $attachment): AttachmentModel => new AttachmentModel(
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
            ),
            $attachments
        );
    }


    public function findOneByAttachable(string $attachableType, int $attachableId, int $attachmentId): ?Attachment
    {
        return $this->attachmentRepository->findOneByAttachable($attachableType, $attachableId, $attachmentId);
    }

    public function deleteByAttachable(string $attachableType, int $attachableId, int $attachmentId): void
    {
        $this->attachmentRepository->deleteByAttachable($attachableType, $attachableId, $attachmentId);
    }

    /**
     * @return AttachmentModel[]
     */
    public function getAttachmentsPaginated(int $page, int $perPage): array
    {
        $attachments = $this->attachmentRepository->getAttachmentsPaginated($page, $perPage);

        return array_map(
            static fn (Attachment $attachment): AttachmentModel => new AttachmentModel(
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
            ),
            $attachments
        );
    }

    /**
     * @param int $attachmentId
     * @return Attachment|null
     */
    public function find(int $attachmentId): ?Attachment
    {
        return $this->attachmentRepository->find($attachmentId);
    }

    /**
     * @param int $attachmentId
     * @return AttachmentModel|null
     */
    public function findModel(int $attachmentId): ?AttachmentModel
    {
        $attachment = $this->attachmentRepository->find($attachmentId);

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
     * @return AttachmentModel[]
     */
    public function findAll(): array
    {
        $attachments = $this->attachmentRepository->findAll();

        return array_map(
            static fn (Attachment $attachment): AttachmentModel => new AttachmentModel(
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
            ),
            $attachments
        );
    }

    /**
     * @param string $title
     * @return AttachmentModel[]
     */
    public function findAttachmentsByTitle(string $title): array
    {
        $attachments = $this->attachmentRepository->findAttachmentsByTitle($title);

        return array_map(
            static fn (Attachment $attachment): AttachmentModel => new AttachmentModel(
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
            ),
            $attachments
        );
    }

    /**
     * @param string $filename
     * @return AttachmentModel[]
     */
    public function findAttachmentsByFilename(string $filename): array
    {
        $attachments = $this->attachmentRepository->findAttachmentsByFilename($filename);

        return array_map(
            static fn (Attachment $attachment): AttachmentModel => new AttachmentModel(
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
            ),
            $attachments
        );
    }

    /**
     * @param DateTimeImmutable $fileDate
     * @return AttachmentModel[]
     */
    public function findAttachmentsByFileDate(DateTimeImmutable $fileDate): array
    {
        $attachments = $this->attachmentRepository->findAttachmentsByFileDate($fileDate);

        return array_map(
            static fn (Attachment $attachment): AttachmentModel => new AttachmentModel(
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
            ),
            $attachments
        );
    }

    /**
     * @param string $path
     * @return AttachmentModel[]
     */
    public function findAttachmentsByPath(string $path): array
    {
        $attachments = $this->attachmentRepository->findAttachmentsByPath($path);

        return array_map(
            static fn (Attachment $attachment): AttachmentModel => new AttachmentModel(
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
            ),
            $attachments
        );
    }

    /**
     * @param Attachment $attachment
     * @return int
     */
    public function create(Attachment $attachment): int
    {
        return $this->attachmentRepository->create($attachment);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->attachmentRepository->update();
    }

    /**
     * @param Attachment $attachment
     * @return void
     */
    public function remove(Attachment $attachment): void
    {
        $this->attachmentRepository->remove($attachment);
    }
}
