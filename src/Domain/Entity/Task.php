<?php //события

namespace App\Domain\Entity;

use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use DateTimeImmutable;
use Webmozart\Assert\Assert as WebmozartAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'task')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Task implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    //статус
    #[ORM\OneToOne(targetEntity: Status::class, inversedBy: 'task', fetch: 'EAGER')]
    private Status $status;

    //дата события
    #[ORM\Column(name: 'date', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $date;

    //описание
    #[ORM\Column(name: 'description', type: 'string', length: 1024, nullable: true)]
    private ?string $description = null;

    //растение
    #[ORM\ManyToOne(targetEntity: Plant::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'plant_id', referencedColumnName: 'id')]
    private Plant $plant;

    private function setCommonFields(
        Status $status,
        Plant $plant,
        DateTimeImmutable $date,
        ?string $description = null,
    ): void {
        $this->setStatusValidate($status);
        $this->setPlantValidate($plant);
        $this->setDateValidate($date);
        $this->setDescriptionValidate($description);
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
        Status $status,
        Plant $plant,
        DateTimeImmutable $date,
        ?string $description = null,
    )
    {
        $this->setCommonFields($status, $plant, $date, $description);
    }

    public function changeFields(
        Status $status,
        Plant $plant,
        DateTimeImmutable $date,
        ?string $description = null,
    ): void
    {
        $this->setCommonFields($status, $plant, $date, $description);
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
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

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'status' => $this->getStatus()->toArray(),
            'plant' => $this->getPlant()->toArray(),
            'date' => $this->getDate()->format('Y-m-d'),
            'description' => $this->getDescription(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
