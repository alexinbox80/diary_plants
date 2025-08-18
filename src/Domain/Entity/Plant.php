<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use App\Domain\Model\OId;
use App\Domain\Model\Price;
use DateTime;
use Webmozart\Assert\Assert as WebmozartAssert;

class Plant implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    private ?int $id = null;

    private ?int $attachemntId = null;

    private ?OId $oid = null;

    private string $title;

    private ?string $description = null;

    private ?string $qrCodeBase64 = null;

    private string $room;

    private ?DateTime $purchaseDate = null;

    private ?DateTime $vaccinationDate = null;

    private ?DateTime $plantingDate = null;

    private ?string $manufacturer = null;

    private ?Price $price = null;

    private bool $isShown = true;

    private ?string $soil = null;

    public function __construct(
        ?int $attachemntId = null,
        string $title,
        ?string $description = null,
        string $room,
        ?DateTime $purchaseDate = null,
        ?DateTime $vaccinationDate = null,
        ?DateTime $plantingDate = null,
        ?string $manufacturer = null,
        ?Price $price = null,
        bool $isShown = true,
        ?string $soil = null
    )
    {
        $this->attachemntId = $attachemntId;
        $this->oid = OId::next();

        WebmozartAssert::stringNotEmpty($title);
        $this->title = $title;
        $this->description = $description;
        $this->qrCodeBase64 = 'data:image/png;base64,' . base64_encode($this->oid);

        WebmozartAssert::stringNotEmpty($room);
        $this->room = $room;
        $this->purchaseDate = $purchaseDate;
        $this->vaccinationDate = $vaccinationDate;
        $this->plantingDate = $plantingDate;
        $this->manufacturer = $manufacturer;
        $this->price = $price;
        $this->isShown = $isShown;
        $this->soil = $soil;
    }

    public function changeFields(
        ?int $attachemntId = null,
        string $title,
        ?string $description = null,
        string $room,
        ?DateTime $purchaseDate = null,
        ?DateTime $vaccinationDate = null,
        ?DateTime $plantingDate = null,
        ?string $manufacturer = null,
        ?Price $price = null,
        bool $isShown = true,
        ?string $soil = null
    ): void
    {
        $this->attachemntId = $attachemntId;
        $this->oid = OId::next();

        WebmozartAssert::stringNotEmpty($title);
        $this->title = $title;
        $this->description = $description;
        $this->qrCodeBase64 = 'data:image/png;base64,' . base64_encode($this->oid);

        WebmozartAssert::stringNotEmpty($room);
        $this->room = $room;
        $this->purchaseDate = $purchaseDate;
        $this->vaccinationDate = $vaccinationDate;
        $this->plantingDate = $plantingDate;
        $this->manufacturer = $manufacturer;
        $this->price = $price;
        $this->isShown = $isShown;
        $this->soil = $soil;
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

    public function getOid(): ?OId
    {
        return $this->oid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getQrCodeBase64(): ?string
    {
        return $this->qrCodeBase64;
    }

    public function getRoom(): string
    {
        return $this->room;
    }

    public function getPurchaseDate(): ?DateTime
    {
        return $this->purchaseDate;
    }

    public function getVaccinationDate(): ?DateTime
    {
        return $this->vaccinationDate;
    }

    public function getPlantingDate(): ?DateTime
    {
        return $this->plantingDate;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function isShown(): bool
    {
        return $this->isShown;
    }

    public function getSoil(): ?string
    {
        return $this->soil;
    }
}
