<?php // Сущность для аналитики

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\ValueObject\Analytic\IntervalMetrics;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;

#[ORM\Table(name: 'analytic')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'analytic__plant_id__ind', columns: ['plant_id'])]
#[ORM\Index(name: 'analytic__group_id__ind', columns: ['group_id'])]
#[ORM\UniqueConstraint(
    name: 'analytic__plant_id__uniq',
    columns: ['plant_id'],
    options: ['where' => '(deleted_at IS NULL)']
)]
class Analytic implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    //количество поливов, средний интервал между поливами в днях
    #[ORM\Embedded(class: IntervalMetrics::class, columnPrefix: false)]
    private IntervalMetrics $wateringMetrics;

    //идентификатор связанной сущности растение
    #[ORM\OneToOne(targetEntity: Plant::class, inversedBy: 'analytic')]
    #[ORM\JoinColumn(name: 'plant_id', referencedColumnName: 'id')]
    private Plant $plant;

    //идентификатор связанной сущности group
    #[ORM\ManyToOne(targetEntity: Group::class, cascade: ['all'], inversedBy: 'analytics')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

    public function __construct(
        Plant $plant,
        Group $group,
    )
    {
        $this->setPlantValidate($plant);
        $this->setGroupValidate($group);

        $this->wateringMetrics = new IntervalMetrics();
    }

    private function setPlantValidate(Plant $plant): void
    {
        $this->plant = $plant;
    }

    private function setGroupValidate(Group $group): void
    {
        $this->group = $group;
    }

    public function changeWateringMetrics(IntervalMetrics $wateringMetrics): self
    {
        $this->wateringMetrics = $wateringMetrics;

        return $this;
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

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function getWateringMetrics(): IntervalMetrics
    {
        return $this->wateringMetrics;
    }
}
