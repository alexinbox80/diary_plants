<?php

namespace App\Domain\Model\Attachment;

use DateTimeImmutable;

class AttachmentModel
{
    public function __construct(
        private readonly int $id,
        private readonly string $filename,
        private readonly string $path,
        private readonly string $title,
        private readonly DateTimeImmutable $fileDate,
        private readonly ?string $description = null,
        private readonly ?int $attachableId = null,
        private readonly ?string $attachableType = null,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getFileDate(): DateTimeImmutable
    {
        return $this->fileDate;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getAttachableId(): ?int
    {
        return $this->attachableId;
    }

    public function getAttachableType(): ?string
    {
        return $this->attachableType;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
