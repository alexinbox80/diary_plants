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
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'attachment')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Attachment implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'photo_link', type: 'string', length: 255, nullable: false)]
    private string $photoLink;

    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: false)]
    private string $title;

    #[ORM\Column(name: 'description', type: 'string', length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'photo_date', type: 'datetime', nullable: false)]
    private DateTime $photoDate;

    #[ORM\Column(name: 'attachable_id', type: 'integer', nullable: true)]
    private ?int $attachableId = null;

    #[ORM\Column(name: 'attachable_type', type: 'string', nullable: true)]
    private ?string $attachableType = null;// Тип сущности (Plant, User и т.п.)

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

    public function getAttachable(): ?EntityInterface
    {
        if (!$this->attachableId || !$this->attachableType) {
            return null;
        }

        $className = $this->attachableType;
        if (!is_subclass_of($className, EntityInterface::class)) {
            throw new \InvalidArgumentException("Class $className does not implement AttachableInterface.");
        }

        //return $entityManager->getReference($className, $this->attachableId);
        return null;
    }

    public function setAttachable(?EntityInterface $attachable): self
    {
        $this->attachableId = $attachable?->getId();
        $this->attachableType = $attachable ? get_class($attachable) : null;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'photo_link' => $this->getPhotoLink(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'photo_date' => $this->getPhotoDate()->format('Y-m-d'),
            //'attachable_id' => $this->getAttachableId(),
            //'attachable_type' => $this->getAttachableType(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
