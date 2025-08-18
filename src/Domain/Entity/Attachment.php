<?php

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

    private string $avatarLink;

    private string $title;

    private ?string $description = null;

    private DateTime $photoDate;

    public function __construct(
        string $avatarLink,
        string $title,
        ?string $description,
        DateTime $photoDate
    )
    {
        $this->avatarLink = $avatarLink;
        $this->title = $title;
        $this->description = $description;
        $this->photoDate = $photoDate;
    }

    public function changeFields(
        string $avatarLink,
        string $title,
        ?string $description,
        DateTime $photoDate
    ): void
    {
        $this->avatarLink = $avatarLink;
        $this->title = $title;
        $this->description = $description;
        $this->photoDate = $photoDate;
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    public function getAvatarLink(): string
    {
        return $this->avatarLink;
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
}
