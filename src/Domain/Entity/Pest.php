<?php //вредители

namespace App\Domain\Entity;

use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use DateTime;
use Webmozart\Assert\Assert as WebmozartAssert;

class Pest extends Preparation implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    private ?int $id = null;

    public function __construct(
        string $title,
        string $manufacturer,
        int $quantity,
        DateTime $useDate,
        ?string $description = null,
    )
    {
        parent::__construct($title, $manufacturer, $quantity, $useDate, $description);
    }

    public function changeFields(
        string $title,
        string $manufacturer,
        int $quantity,
        DateTime $useDate,
        ?string $description = null,
    ): void
    {
        $this->changeFields(
            $title,
            $manufacturer,
            $quantity,
            $useDate,
            $description
        );
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    public function toArray(): array
    {
        return
            array_merge(
                parent::toArray(),
                [
                    'id' => $this->id,
                    'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
                    'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
                ]
            );
    }
}
