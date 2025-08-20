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

class Task  implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    private ?int $id = null;

    private Status $status;

    private DateTime $date;

    private ?string $description = null;

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
