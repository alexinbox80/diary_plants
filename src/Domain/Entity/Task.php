<?php //события

namespace App\Domain\Entity;

use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use DateTime;
use Webmozart\Assert\Assert as WebmozartAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'task')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'task__status_id__ind', columns: ['status_id'])]
#[ORM\UniqueConstraint(name: 'task__status_id__uniq', fields: ['status'], options: ['where' => '(deleted_at IS NULL)'])]
#[ORM\Index(name: 'task__plant_id__ind', columns: ['plant_id'])]
#[ORM\UniqueConstraint(name: 'task__plant_id__uniq', fields: ['plant'], options: ['where' => '(deleted_at IS NULL)'])]
class Task  implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Status::class, inversedBy: 'task', fetch: 'EAGER')]
    private Status $status;

    #[ORM\Column(name: 'date', type: 'datetime', nullable: false)]
    private DateTime $date;

    #[ORM\Column(name: 'description', type: 'string', length: 1024, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Plant::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'plant_id', referencedColumnName: 'id')]
    private Plant $plant;

    public function __construct(
        Status $status,
        DateTime $date,
        ?string $description = null,
    )
    {
        $this->status = $status;
        $this->date = $date;
        $this->description = $description;
    }

    public function changeFields(
        Status $status,
        DateTime $date,
        ?string $description = null,
    ): void
    {
        $this->status = $status;
        $this->date = $date;
        $this->description = $description;
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

    public function getDate(): DateTime
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
            'date' => $this->getDate()->format('Y-m-d'),
            'description' => $this->getDescription(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
