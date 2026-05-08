<?php //полив

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\ValueObject\Watering\WateringDetails;
use App\Domain\Entity\Interfaces\AttachableInterface;
use App\Domain\Entity\Interfaces\GroupOwnedInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;

#[ORM\Table(name: 'watering')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'watering__marker_id__ind', columns: ['marker_id'])]
#[ORM\Index(name: 'watering__group_id__ind', columns: ['group_id'])]
#[ORM\UniqueConstraint(
    name: 'watering__group_id_marker_id__uniq',
    columns: ['group_id', 'marker_id'],
    options: ['where' => '(deleted_at IS NULL)']
)]
class Watering implements EntityInterface, AttachableInterface, GroupOwnedInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    //тип воды: отстоянная, фильтрованная, осмос, дождевая, водопроводная
    //метод: в грунт, в поддон, опрыскивание, фитиль
    #[ORM\Embedded(class: WateringDetails::class, columnPrefix: false)]
    private WateringDetails $details;

    //описание
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    //комментарий
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $comment = null;

    //идентификатор связанной сущности маркер
    #[ORM\ManyToOne(targetEntity: Marker::class)]
    #[ORM\JoinColumn(name: 'marker_id', referencedColumnName: 'id', nullable: false)]
    private Marker $marker;

    //идентификатор связанной сущности group
    #[ORM\ManyToOne(targetEntity: Group::class, cascade: ['all'], inversedBy: 'waterings')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

    public function __construct(Group $group, Marker $marker, WateringDetails $details)
    {
        $this->setFields($group, $marker, $details);
    }

    public function updateFields(Group $group, Marker $marker, WateringDetails $details): self
    {
        $this->setFields($group, $marker, $details);

        return $this;
    }

    private function setFields(Group $group, Marker $marker, WateringDetails $details): void
    {
        $this->setGroupValidate($group);
        $this->setMarkerValidate($marker);
        $this->details = $details;
    }

    private function setGroupValidate(Group $group): void
    {
        $this->group = $group;
    }

    private function setMarkerValidate(Marker $marker): void
    {
        $this->marker = $marker;
    }

    public function moveToGroup(Group $group): self
    {
        if ($this->getGroupId() === $group->getId()) {
            return $this;
        }

        $this->group = $group;

        $this->marker->moveToGroup($group);

        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    public function getDetails(): WateringDetails
    {
        return $this->details;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getMarker(): Marker
    {
        return $this->marker;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function getGroupId(): int
    {
        return $this->group->getId();
    }
}
