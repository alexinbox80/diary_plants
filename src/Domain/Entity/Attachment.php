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
                    └── id/
                        ├── original/
                        │   └── abc123.jpg
                        └── thumbnails/
                            └── thumb_abc123.jpg
         * */
namespace App\Domain\Entity;

use DateTimeImmutable;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\AttachableInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'attachment')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'attachment__attachable__ind', columns: ['attachable_type', 'attachable_id'])]
class Attachment implements EntityInterface, AttachableInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    //имя файла
    #[ORM\Column(name: 'filename', type: 'string', length: 255, nullable: false)]
    private string $filename;

    //путь к файлу
    #[ORM\Column(name: 'path', type: 'string', length: 255, nullable: false)]
    private string $path;

    //тип майм
    #[ORM\Column(name: 'mime_type', type: 'string', length: 50, nullable: false)]
    private string $mimeType;

    //альтернативный заголовок
    #[ORM\Column(name: 'alt', type: 'string', length: 255, nullable: false)]
    private string $alt;

    //заголовок
    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: false)]
    private string $title;

    //описание
    #[ORM\Column(name: 'description', type: 'string', length: 1024, nullable: true)]
    private ?string $description = null;

    //дата загрузки файла
    #[ORM\Column(name: 'file_date', type: 'datetime_immutable', nullable: false)]
    private DateTimeImmutable $fileDate;

    //идентификатор связанной сущности
    #[ORM\Column(name: 'attachable_id', type: 'integer', nullable: true)]
    private ?int $attachableId = null;

    //тип связанной сущности
    #[ORM\Column(name: 'attachable_type', type: 'string', nullable: true)]
    private ?string $attachableType = null;// Тип сущности (Plant, User и т.п.)

    public function __construct(
        string $filename,
        string $path,
        string $mimeType,
        string $alt,
        string $title,
        DateTimeImmutable $fileDate,
        ?string $description = null,
        ?int $attachableId = null,
        ?string $attachableType = null
    )
    {
        $this->setCommonFields($filename, $path, $mimeType, $alt, $title, $fileDate, $description, $attachableId, $attachableType);
    }

    private function setCommonFields(
        string $filename,
        string $path,
        string $mimeType,
        string $alt,
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

        WebmozartAssert::stringNotEmpty($mimeType);
        $this->mimeType = $mimeType;

        WebmozartAssert::stringNotEmpty($alt);
        $this->alt = $alt;

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
        string $mimeType,
        string $alt,
        string $title,
        DateTimeImmutable $fileDate,
        ?string $description = null,
        ?int $attachableId = null,
        ?string $attachableType = null
    ): void
    {
        $this->setCommonFields($filename, $path, $mimeType, $alt, $title, $fileDate, $description, $attachableId, $attachableType);
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
            'mimeType' => $this->getMimeType(),
            'alt' => $this->getAlt(),
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
