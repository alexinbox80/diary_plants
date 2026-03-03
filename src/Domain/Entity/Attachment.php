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

use Doctrine\ORM\Mapping as ORM;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use App\Domain\ValueObject\Attachment\FileInfo;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\AttachableInterface;
use App\Domain\ValueObject\Attachment\DisplaySettings;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\ValueObject\Attachment\AttachableReference;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;

#[ORM\Table(name: 'attachment')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'attachment__attachable__ind', columns: ['attachable_type', 'attachable_id'])]
#[ORM\Index(name: 'attachment__group_id__ind', columns: ['group_id'])]
class Attachment implements EntityInterface, AttachableInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    //заголовок, описание, альтернативный заголовок, показывать приложение или нет
    #[ORM\Embedded(class: DisplaySettings::class, columnPrefix: false)]
    private DisplaySettings $displaySettings;

    //имя файла, путь к файлу, тип майм файла, дата загрузки файла
    #[ORM\Embedded(class: FileInfo::class, columnPrefix: false)]
    private FileInfo $fileInfo;

    //идентификатор связанной сущности, тип связанной сущности
    #[ORM\Embedded(class: AttachableReference::class, columnPrefix: false)]
    private AttachableReference $target;

    //идентификатор связанной сущности group
    #[ORM\ManyToOne(targetEntity: Group::class, cascade: ['all'], fetch: 'EAGER', inversedBy: 'attachments')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

    public function __construct(
        Group $group,
        DisplaySettings $displaySettings,
    )
    {
        $this->group = $group;
        $this->displaySettings = $displaySettings;

        $this->fileInfo = new FileInfo();
        $this->target = new AttachableReference();
    }

    public function moveToGroup(Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function updateDisplaySettings(DisplaySettings $displaySettings): self
    {
        $this->displaySettings = $displaySettings;

        return $this;
    }

    public function updateFileInfo(FileInfo $fileInfo): self
    {
        $this->fileInfo = $fileInfo;

        return $this;
    }

    public function updateTarget(AttachableReference $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function getDisplaySettings(): DisplaySettings
    {
        return $this->displaySettings;
    }

    public function getFileInfo(): FileInfo
    {
        return $this->fileInfo;
    }

    public function getTarget(): AttachableReference
    {
        return $this->target;
    }
}
