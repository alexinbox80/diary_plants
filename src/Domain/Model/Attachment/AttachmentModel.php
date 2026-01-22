<?php

namespace App\Domain\Model\Attachment;

use DateTimeImmutable;
use DateTimeZone;

class AttachmentModel
{
    public function __construct(
        private readonly int $id,
        private readonly string $filename,
        private readonly string $path,
        private readonly string $mimeType,
        private readonly string $alt,
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

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getAlt(): string
    {
        return $this->alt;
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

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => '#',
            'img_tag' => 'Изображение',
            'filename' => 'Имя файла',
            'path' => 'Путь к файлу',
            'mime_type' => 'Тип файла',
            'alt' => 'Альтернативный текст',
            'title' => 'Название',
            'description' => 'Описание',
            'file_date' => 'Дата файла',
            'attachable_id' => 'ID сущности',
            'attachable_type' => 'Тип сущности',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления'
        ];
    }

    public function toArray(): array
    {
        $timezone = new DateTimeZone('Europe/Moscow');

        return [
            'id' => $this->getId(),
            'img_tag' => $this->getPath() . $this->getFilename(),
            'filename' => $this->getFilename(),
            'path' => $this->getPath(),
            'mime_type' => $this->getMimeType(),
            'alt' => $this->getAlt(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'file_date' => $this->getFileDate()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'attachable_id' => $this->getAttachableId(),
            'attachable_type' => $this->getAttachableType(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
        ];
    }
}
