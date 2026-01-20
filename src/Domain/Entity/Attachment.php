<?php //вложения
/*
 * структура папок на фс
 * public/
        └── uploads/
            └── videos/
            └── images/
                └── plant/
                    offspring/
                    pest/
                    ...
                    └── uid/
                        ├── original/
                        │   └── abc123.jpg
                        └── thumbnails/
                            └── thumb_abc123.jpg
         * */
namespace App\Domain\Entity;

use DateTimeImmutable;
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
#[ORM\Index(name: 'attachment__attachable__ind', columns: ['attachable_type', 'attachable_id'])]
class Attachment implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'filename', type: 'string', length: 255, nullable: false)]
    private string $filename;

    #[ORM\Column(name: 'path', type: 'string', length: 255, nullable: false)]
    private string $path;

    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: false)]
    private string $title;

    #[ORM\Column(name: 'description', type: 'string', length: 1024, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'file_date', type: 'datetime_immutable', nullable: false)]
    private DateTimeImmutable $fileDate;

    #[ORM\Column(name: 'attachable_id', type: 'integer', nullable: true)]
    private ?int $attachableId = null;

    #[ORM\Column(name: 'attachable_type', type: 'string', nullable: true)]
    private ?string $attachableType = null;// Тип сущности (Plant, User и т.п.)

    public function __construct(
        string $filename,
        string $path,
        string $title,
        DateTimeImmutable $fileDate,
        ?string $description = null,
        ?int $attachableId = null,
        ?string $attachableType = null
    )
    {
        $this->setCommonFields($filename, $path, $title, $fileDate, $description, $attachableId, $attachableType);
    }

    private function setCommonFields(
        string $filename,
        string $path,
        string $title,
        DateTimeImmutable $fileDate,
        ?string $description = null,
        ?int $attachableId = null,
        ?string $attachableType = null
    ): void {
        WebmozartAssert::stringNotEmpty($filename);
        $this->filename = $filename;

        WebmozartAssert::stringNotEmpty($path);
        $this->path = $path;

        WebmozartAssert::stringNotEmpty($title);
        $this->title = $title;

        $this->description = $description;
        $this->fileDate = $fileDate;

        $this->attachableId = $attachableId;
        $this->attachableType = $attachableType;
    }

    public function changeFields(
        string $filename,
        string $path,
        string $title,
        DateTimeImmutable $fileDate,
        ?string $description = null,
        ?int $attachableId = null,
        ?string $attachableType = null
    ): void
    {
        $this->setCommonFields($filename, $path, $title, $fileDate, $description, $attachableId, $attachableType);
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getFileDate(): DateTimeImmutable
    {
        return $this->fileDate;
    }

    public function getAttachableId(): ?int
    {
        return $this->attachableId;
    }

    public function getAttachableType(): ?string
    {
        return $this->attachableType;
    }

    public function setAttachableType(?string $attachableType = null): void
    {
        $this->attachableType = $attachableType;
    }

    public function setAttachableId(?int $attachableId = null): void
    {
        $this->attachableId = $attachableId;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'filename' => $this->getFilename(),
            'path' => $this->getPath(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'file_date' => $this->getFileDate()->format('Y-m-d'),
            'attachable_id' => $this->getAttachableId(),
            'attachable_type' => $this->getAttachableType(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
