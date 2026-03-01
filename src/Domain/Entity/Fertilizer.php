<?php //удобрения

namespace App\Domain\Entity;

use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'fertilizer')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'fertilizer__plant_id__ind', columns: ['plant_id'])]
#[ORM\Index(name: 'fertilizer__group_id__ind', columns: ['group_id'])]
class Fertilizer extends Preparation implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Plant::class, inversedBy: 'fertilizers')]
    #[ORM\JoinColumn(name: 'plant_id', referencedColumnName: 'id')]
    private Plant $plant;

    //идентификатор связанной сущности group
    #[ORM\ManyToOne(targetEntity: Group::class, cascade: ['all'], fetch: 'EAGER', inversedBy: 'fertilizers')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

    public function __construct(
        Group $group,
        Plant $plant,
        string $title,
        int $quantity,
        string $letter,
        string $manufacturer,
        ?string $description = null,
        ?string $comment = null
    )
    {
        parent::__construct($title, $quantity, $letter, $manufacturer, $description, $comment);

        $this->group = $group;
        $this->plant = $plant;
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

    public function getPlant(): Plant
    {
        return $this->plant;
    }

    public function changeFieldsWithPlant(
        Group    $group,
        Plant    $plant,
        string   $title,
        int      $quantity,
        string   $letter,
        string   $manufacturer,
        ?string  $description = null,
        ?string  $comment = null,
    ): void
    {
        parent::changeFields($title, $quantity, $letter, $manufacturer, $description, $comment);

        $this->group = $group;
        $this->plant = $plant;
    }
}
