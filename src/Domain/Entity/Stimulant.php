<?php //стимуляторы

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\AttachableInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\ValueObject\Preparation\PreparationVolume;
use App\Domain\ValueObject\Preparation\PreparationDetails;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;

#[ORM\Table(name: 'stimulant')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'stimulant__marker_id__ind', columns: ['marker_id'])]
#[ORM\Index(name: 'stimulant__group_id__ind', columns: ['group_id'])]
class Stimulant extends Preparation implements EntityInterface, AttachableInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    //идентификатор связанной сущности маркер
    #[ORM\ManyToOne(targetEntity: Marker::class, inversedBy: 'stimulants')]
    #[ORM\JoinColumn(name: 'marker_id', referencedColumnName: 'id', nullable: false)]
    private Marker $marker;

    //идентификатор связанной сущности group
    #[ORM\ManyToOne(targetEntity: Group::class, cascade: ['all'], fetch: 'EAGER', inversedBy: 'stimulants')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

    public function __construct(
        Group $group,
        Marker $marker,
        string $title,
        PreparationVolume $volume,
        PreparationDetails $details = new PreparationDetails()
    )
    {
        parent::__construct($title, $volume, $details);

        $this->group = $group;
        $this->marker = $marker;
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

    public function getMarker(): Marker
    {
        return $this->marker;
    }

    public function changeFieldsWithMarker(
        Group $group,
        Marker $marker,
        string $title,
        PreparationVolume $volume,
        PreparationDetails $details
    ): void
    {
        parent::changeFields($title, $volume, $details);

        $this->group = $group;
        $this->marker = $marker;
    }
}
