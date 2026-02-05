<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Attachment;
use App\Domain\Model\Attachment\AttachmentModel;
use DateTimeImmutable;

interface AttachmentRepositoryInterface
{
    public function findByAttachable(string $attachableType, int $attachableId): array;
    public function findByAttachableWithDeleted(string $attachableType, int $attachableId): array;
    public function findOneByAttachable(string $attachableType, int $attachableId, int $attachmentId): ?Attachment;
    public function deleteByAttachable(string $attachableType, int $attachableId, int $attachmentId): void;
    public function getAttachmentsPaginated(int $page, int $perPage): array;
    public function find(int $attachmentId): ?Attachment;
    public function findModel(int $attachmentId): ?AttachmentModel;
    public function findAll(): array;
    public function findAttachmentsByTitle(string $title): array;
    public function findAttachmentsByFilename(string $filename): array;
    public function findAttachmentsByFileDate(DateTimeImmutable $fileDate): array;
    public function findAttachmentsByPath(string $path): array;
    public function create(Attachment $attachment): int;
    public function update(): void;
    public function remove(Attachment $attachment): void;
    public function toModel(Attachment $attachment, bool $addRelations = false): AttachmentModel;
}
