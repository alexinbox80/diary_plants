<?php

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

    private ?int $attachemntId = null;

    private ?DateTime $fruitingDate = null;

    private ?DateTime $floweringDate = null;

    private ?int $mass = null;

    private ?string $color = null;

    private ?string $flavor = null;

    private ?int $quantity = null;

    public function __construct(
        ?int $attachemntId = null,
        ?DateTime $fruitingDate = null,
        ?DateTime $floweringDate = null,
        ?int $mass = null,
        ?string $color = null,
        ?string $flavor = null,
        ?int $quantity = null
    ) {
        $this->attachemntId = $attachemntId;
        $this->fruitingDate = $fruitingDate;
        $this->floweringDate = $floweringDate;
        $this->mass = $mass;
        $this->color = $color;
        $this->flavor = $flavor;
        $this->quantity = $quantity;
    }

    public function changeFields(
        ?int $attachemntId = null,
        ?DateTime $fruitingDate = null,
        ?DateTime $floweringDate = null,
        ?int $mass = null,
        ?string $color = null,
        ?string $flavor = null,
        ?int $quantity = null
    ): void
    {
        $this->attachemntId = $attachemntId;
        $this->fruitingDate = $fruitingDate;
        $this->floweringDate = $floweringDate;
        $this->mass = $mass;
        $this->color = $color;
        $this->quantity = $quantity;
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    public function getAttachemntId(): ?int
    {
        return $this->attachemntId;
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
}
