<?php //пересадка растений

namespace App\Domain\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\ValueObject\Repotting\RepottingDetails;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;

#[ORM\Table(name: 'repotting')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'repotting__plant_id__ind', columns: ['plant_id'])]
#[ORM\Index(name: 'repotting__group_id__ind', columns: ['group_id'])]
class Repotting implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    //дата пересадки
    #[ORM\Column(name: 'repotted_at', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $repottedAt;

    //грунт
    #[ORM\Column(name: 'substrate', type: 'string', length: 1024, nullable: true, options: ['default' => null])]
    private ?string $substrate = null;

    //размер горшка диаметры см. 10, 12, 14
    //тип пересадки перевалка, полная пересадка, деление куста, замена верхнего слоя, пикировка, посадка саженца, обрезка корней, реанимационная пересадка
    //тип горшка
    #[ORM\Embedded(class: RepottingDetails::class, columnPrefix: false)]
    private RepottingDetails $details;

    //комментарий
    #[ORM\Column(name: 'comment', type: 'text', length: 1024, nullable: true)]
    private ?string $comment = null;

    //идентификатор растения
    #[ORM\ManyToOne(targetEntity: Plant::class, inversedBy: 'repottings')]
    #[ORM\JoinColumn(name: 'plant_id', referencedColumnName: 'id', nullable: false)]
    private Plant $plant;

    //идентификатор связанной сущности group
    #[ORM\ManyToOne(targetEntity: Group::class, cascade: ['all'], inversedBy: 'repottings')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

    private function setCommonFields(
        Group $group,
        Plant $plant,
        DateTimeImmutable $repottedAt,
        RepottingDetails $details
    ): void {
        $this->moveToGroup($group);
        $this->moveToPlant($plant);
        $this->setRepottedAtValidate($repottedAt);
        $this->setRepottingDetailsValidate($details);

    }

    private function setRepottedAtValidate(DateTimeImmutable $repottedAt): void
    {
        $this->repottedAt = $repottedAt;
    }

    private function setRepottingDetailsValidate(RepottingDetails $details): void
    {
        $this->details = $details;
    }

    public function changeFields(
        Group $group,
        Plant $plant,
        DateTimeImmutable $repottedAt,
        RepottingDetails $details
    ): void
    {
        $this->setCommonFields(
            $group,
            $plant,
            $repottedAt,
            $details
        );
    }

    public function __construct(
        Group $group,
        Plant $plant,
        DateTimeImmutable $repottedAt,
        RepottingDetails $details
    )
    {
        $this->setCommonFields(
            $group,
            $plant,
            $repottedAt,
            $details
        );
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    public function getRepottedAt(): DateTimeImmutable
    {
        return $this->repottedAt;
    }

    public function getSubstrate(): ?string
    {
        return $this->substrate;
    }

    public function setSubstrate(?string $substrate = null): self
    {
        $this->substrate = $substrate;
        return $this;
    }

    public function getDetails(): RepottingDetails
    {
        return $this->details;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment = null): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getPlant(): Plant
    {
        return $this->plant;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function moveToGroup(Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function moveToPlant(Plant $plant): self
    {
        $this->plant = $plant;

        return $this;
    }
}
