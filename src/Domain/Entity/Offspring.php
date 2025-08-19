<?php //плод

namespace App\Domain\Entity;

use DateTime;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;

class Offspring implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    private ?int $id = null;

    private ?Attachment $attachment = null;

    private ?DateTime $fruitingDate = null;

    private ?DateTime $floweringDate = null;

    private ?int $mass = null;

    private ?string $color = null;

    private ?string $flavor = null;

    private ?int $quantity = null;

    public function __construct(
        ?Attachment $attachment = null,
        ?DateTime $fruitingDate = null,
        ?DateTime $floweringDate = null,
        ?int $mass = null,
        ?string $color = null,
        ?string $flavor = null,
        ?int $quantity = null
    ) {
        if ($attachment) {
            $attachment->setAttachableType(Offspring::class);
            $attachment->setAttachableId($this->getId());
        }

        $this->attachment = $attachment;
        $this->fruitingDate = $fruitingDate;
        $this->floweringDate = $floweringDate;
        $this->mass = $mass;
        $this->color = $color;
        $this->flavor = $flavor;
        $this->quantity = $quantity;
    }

    public function changeFields(
        ?Attachment $attachment = null,
        ?DateTime $fruitingDate = null,
        ?DateTime $floweringDate = null,
        ?int $mass = null,
        ?string $color = null,
        ?string $flavor = null,
        ?int $quantity = null
    ): void
    {
        if ($attachment) {
            $attachment->setAttachableType(Offspring::class);
            $attachment->setAttachableId($this->getId());
        }
        $this->attachment = $attachment;

        $this->fruitingDate = $fruitingDate;
        $this->floweringDate = $floweringDate;
        $this->mass = $mass;
        $this->color = $color;
        $this->flavor = $flavor;
        $this->quantity = $quantity;
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    public function getAttachment(): ?Attachment
    {
        return $this->attachment;
    }

    public function getFruitingDate(): ?DateTime
    {
        return $this->fruitingDate;
    }

    public function getFloweringDate(): ?DateTime
    {
        return $this->floweringDate;
    }

    public function getMass(): ?int
    {
        return $this->mass;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getFlavor(): ?string
    {
        return $this->flavor;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'attachment' => $this->getAttachment(),
            'fruiting_date' => $this->getFruitingDate(),
            'flowering_date' => $this->getFloweringDate(),
            'mass' => $this->getMass(),
            'color' => $this->getColor(),
            'flavor' => $this->getFlavor(),
            'quantity' => $this->getQuantity(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
