<?php

namespace App\Domain\Model\Attachment;

use App\Domain\Entity\Group;
use App\Domain\Entity\Interfaces\AttachableInterface;
use App\Domain\ValueObject\Enum\AttachableType;
use DateTimeImmutable;
use DateTimeZone;

class AttachmentModel
{
    public function __construct(
        private readonly int $id,
        private readonly int $groupId,
        private readonly ?Group $group = null,
        private readonly bool $isShown,
        private readonly string $alt,
        private readonly string $title,
        private readonly DateTimeImmutable $fileDate,
        private readonly ?string $filename = null,
        private readonly ?string $path = null,
        private readonly ?string $mimeType = null,
        private readonly ?string $description = null,
        private readonly ?int $attachableId = null,
        private readonly ?string $attachableType = null,
        private readonly ?AttachableInterface $attachable = null,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function isShown(): bool
    {
        return $this->isShown;
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

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
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

    public function getAttachable(): ?AttachableInterface
    {
        return $this->attachable;
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
            'group_id' => 'Идентификатор группы',
            'group_title' => 'Название группы',
            'img_tag' => 'Изображение',
            'is_shown' => 'Опубликовать',
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
            'group_id' => $this->getGroupId(),
            'group_title' => $this->getGroup()->getTitle(),
            'img_tag' => (!empty($this->getPath()) && !empty($this->getFilename())) ? $this->getPath() . $this->getFilename() : null,
            'is_shown' => $this->isShown() ? 'Да' : 'Нет',
            'filename' => $this->getFilename(),
            'path' => $this->getPath(),
            'mime_type' => $this->getMimeType(),
            'alt' => $this->getAlt(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'file_date' => $this->getFileDate()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            //'attachable_id' => $this->getAttachableId(),
            'attachable_id' => $this->getAttachable()?->getId(),
            'attachable_type' => AttachableType::getLabel($this->getAttachableType()),
            'attachable' => $this->getAttachable(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
        ];
    }
}
