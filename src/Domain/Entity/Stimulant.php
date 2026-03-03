<?php //стимуляторы

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\ValueObject\Preparation\PreparationVolume;
use App\Domain\ValueObject\Preparation\PreparationDetails;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;

#[ORM\Table(name: 'stimulant')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'stimulant__plant_id__ind', columns: ['plant_id'])]
#[ORM\UniqueConstraint(name: 'stimulant__letter__uniq', columns: ['letter'], options: ['where' => '(deleted_at IS NULL)'])]
class Stimulant extends Preparation implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    //связь с растениями
    #[ORM\ManyToOne(targetEntity: Plant::class, inversedBy: 'stimulants')]
    #[ORM\JoinColumn(name: 'plant_id', referencedColumnName: 'id')]
    private Plant $plant;

    //идентификатор связанной сущности group
    #[ORM\ManyToOne(targetEntity: Group::class, cascade: ['all'], fetch: 'EAGER', inversedBy: 'stimulants')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

    public function __construct(
        Group $group,
        Plant $plant,
        string $title,
        PreparationVolume $volume,
        PreparationDetails $details = new PreparationDetails()
    )
    {
        parent::__construct($title, $volume, $details);

        $this->group = $group;
        $this->plant = $plant;
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    public function getPlant(): Plant
    {
        return $this->plant;
    }

    public function changeFieldsWithPlant(
        Group $group,
        Plant $plant,
        string $title,
        PreparationVolume $volume,
        PreparationDetails $details
    ): void
    {
        parent::changeFields($title, $volume, $details);

        $this->group = $group;
        $this->plant = $plant;
    }
}
