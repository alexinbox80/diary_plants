<?php //вложения

namespace App\Domain\Entity;

use DateTime;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;

class Attachment implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    private ?int $id = null;

    private string $photoLink;

    private string $title;

    private ?string $description = null;

    private DateTime $photoDate;

    private ?int $attachableId = null;

    private ?string $attachableType = null; // Тип сущности (Plant, User и т.п.)

    public function __construct(
        string $photoLink,
        string $title,
        DateTime $photoDate,
        ?string $description = null,
        ?int $attachableId = null,
        ?string $attachableType = null
    )
    {
        WebmozartAssert::stringNotEmpty($photoLink);
        $this->photoLink = $photoLink;

        WebmozartAssert::stringNotEmpty($title);
        $this->title = $title;

        $this->description = $description;
        $this->photoDate = $photoDate;

        $this->attachableId = $attachableId;
        $this->attachableType = $attachableType;
    }

    public function changeFields(
        string $photoLink,
        string $title,
        ?string $description,
        DateTime $photoDate,
        int $attachableId,
        string $attachableType
    ): void
    {
        $this->photoLink = $photoLink;
        $this->title = $title;
        $this->description = $description;
        $this->photoDate = $photoDate;
        $this->attachableId = $attachableId;
        $this->attachableType = $attachableType;
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    public function getPhotoLink(): string
    {
        return $this->photoLink;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPhotoDate(): DateTime
    {
        return $this->photoDate;
    }

    public function getAttachableType(): ?string
    {
        return $this->attachableType;
    }

    public function setAttachableType(?string $type): void
    {
        $this->attachableType = $type;
    }

    public function getAttachableId(): ?int
    {
        return $this->attachableId;
    }

    public function setAttachableId(?int $id): void
    {
        $this->attachableId = $id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'photo_link' => $this->getPhotoLink(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'photo_date' => $this->getPhotoDate()->format('Y-m-d'),
            'attachable_id' => $this->getAttachableId(),
            'attachable_type' => $this->getAttachableType(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
