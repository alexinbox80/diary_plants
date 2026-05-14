<?php

namespace App\Domain\Model\Attachment;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Attachment;
use App\Domain\Model\Group\GroupModel;
use App\Domain\ValueObject\Enum\Timezone;
use App\Domain\Model\Interfaces\AttachableModelInterface;
use App\Domain\ValueObject\Enum\Attachment\AttachableType;

class AttachmentModel
{
    public function __construct(
        private readonly int $id,
        private readonly int $groupId,
        private readonly bool $isShown,
        private readonly string $alt,
        private readonly string $title,
        private readonly DateTimeImmutable $fileDate,
        private readonly ?GroupModel $group = null,
        private readonly ?string $filename = null,
        private readonly ?string $path = null,
        private readonly ?string $mimeType = null,
        private readonly ?string $description = null,
        private readonly ?int $attachableId = null,
        private readonly ?string $attachableType = null,
        private readonly ?AttachableModelInterface $attachable = null,
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

    public function getGroup(): ?GroupModel
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

    public function getAttachable(): ?AttachableModelInterface
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

    /**
     * @param Attachment $attachment
     * @param GroupModel|null $groupModel
     * @param AttachableModelInterface|null $attachableModel
     * @return AttachmentModel
     */
    public static function fromEntity(
        Attachment $attachment,
        ?GroupModel $groupModel = null,
        ?AttachableModelInterface $attachableModel = null
    ): self {
        return new self(
            $attachment->getId(),
            $attachment->getGroup()->getId(),
            $attachment->getDisplaySettings()->isShown(),
            $attachment->getDisplaySettings()->getAlt(),
            $attachment->getDisplaySettings()->getTitle(),
            $attachment->getFileInfo()->getFileDate(),
            $groupModel,
            $attachment->getFileInfo()->getFilename(),
            $attachment->getFileInfo()->getPath(),
            $attachment->getFileInfo()->getMimeType(),
            $attachment->getDisplaySettings()->getDescription(),
            $attachment->getTarget()->getAttachableId(),
            $attachment->getTarget()->getAttachableType()->value,
            $attachableModel,
            $attachment->getCreatedAt(),
            $attachment->getUpdatedAt()
        );
    }

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => 'table.attachment.header.id',
            'group_id' => 'table.attachment.header.group_id',
            'group_title' => 'table.attachment.header.group_title',
            'img_tag' => 'table.attachment.header.img_tag',
            'is_shown_label' => 'table.attachment.header.is_shown_label',
            'filename' => 'table.attachment.header.filename',
            'path' => 'table.attachment.header.path',
            'mime_type' => 'table.attachment.header.mime_type',
            'alt' => 'table.attachment.header.alt',
            'title' => 'table.attachment.header.title',
            'description' => 'table.attachment.header.description',
            'file_date' => 'table.attachment.header.file_date',
            'attachable_id' => 'table.attachment.header.attachable_id',
            'attachable_type' => 'table.attachment.header.attachable_type',
            'created_at' => 'table.attachment.header.created_at',
            'updated_at' => 'table.attachment.header.updated_at'
        ];
    }

    public function toArray(?Timezone $tz = null): array
    {
        if (is_null($tz)) {
            $timezone = new DateTimeZone('Europe/Moscow');
        } else {
            $timezone = new DateTimeZone($tz->value);
        }

        return [
            'id' => $this->getId(),
            'group_id' => $this->getGroupId(),
            'group_title' => $this->getGroup()?->getTitle(),
            'img_tag' => (!empty($this->getPath()) && !empty($this->getFilename())) ? $this->getPath() . $this->getFilename() : null,
            'is_shown_label' => $this->isShown() ? 'Да' : 'Нет',
            'is_shown' => $this->isShown(),
            'filename' => $this->getFilename(),
            'path' => $this->getPath(),
            'mime_type' => $this->getMimeType(),
            'alt' => $this->getAlt(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'file_date' => $this->getFileDate()->format('d.m.Y'),
            'attachable_id' => $this->getAttachable()?->getId(),
            'attachable_type' => AttachableType::getLabel($this->getAttachableType()),
            'attachable' => $this->getAttachable(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
        ];
    }
}
