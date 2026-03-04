<?php //события

namespace App\Domain\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;

#[ORM\Table(name: 'task')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'task__group_id__ind', columns: ['group_id'])]
class Task implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    //дата события
    #[ORM\Column(name: 'date', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $date;

    //описание
    #[ORM\Column(name: 'description', type: 'string', length: 1024, nullable: true)]
    private ?string $description = null;

    //растение
    #[ORM\ManyToOne(targetEntity: Plant::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'plant_id', referencedColumnName: 'id', nullable: false)]
    private Plant $plant;

    //статус
    #[ORM\ManyToOne(targetEntity: Status::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private Status $status;

    //идентификатор связанной сущности group
    #[ORM\ManyToOne(targetEntity: Group::class, cascade: ['all'], fetch: 'EAGER', inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

    private function setCommonFields(
        Group $group,
        Status $status,
        Plant $plant,
        DateTimeImmutable $date,
        ?string $description = null,
    ): void {
        $this->setGroupValidate($group);
        $this->setStatusValidate($status);
        $this->setPlantValidate($plant);
        $this->setDateValidate($date);
        $this->setDescriptionValidate($description);
    }

    private function setGroupValidate(Group $group): void
    {
        $this->group = $group;
    }

    private function setStatusValidate(Status $status): void
    {
        $this->status = $status;
    }

    private function setPlantValidate(Plant $plant): void
    {
        $this->plant = $plant;
    }

    private function setDateValidate(DateTimeImmutable $date): void
    {
        WebmozartAssert::isInstanceOf($date, DateTimeImmutable::class, 'Use date must be a DateTime instance');
        $this->date = $date;
    }

    private function setDescriptionValidate(?string $description = null): void
    {
        $this->description = $description;
    }

    public function __construct(
        Group $group,
        Status $status,
        Plant $plant,
        DateTimeImmutable $date,
        ?string $description = null,
    )
    {
        $this->setCommonFields($group, $status, $plant, $date, $description);
    }

    public function changeFields(
        Group $group,
        Status $status,
        Plant $plant,
        DateTimeImmutable $date,
        ?string $description = null,
    ): void
    {
        $this->setCommonFields($group, $status, $plant, $date, $description);
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

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getPlant(): Plant
    {
        return $this->plant;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
