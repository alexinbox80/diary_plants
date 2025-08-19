<?php //растение

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

    private ?Attachment $attachment = null;

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
        string $title,
        string $room,
        bool $isShown = true,
        ?Attachment $attachment = null,
        ?string $description = null,
        ?DateTime $purchaseDate = null,
        ?DateTime $vaccinationDate = null,
        ?DateTime $plantingDate = null,
        ?string $manufacturer = null,
        ?Price $price = null,
        ?string $soil = null
    )
    {
        WebmozartAssert::stringNotEmpty($title);
        $this->title = $title;
        WebmozartAssert::stringNotEmpty($room);
        $this->room = $room;

        $this->isShown = $isShown;

        if ($attachment) {
            $attachment->setAttachableType(Plant::class);
            $attachment->setAttachableId($this->getId());
        }

        $this->attachment = $attachment;
        $this->description = $description;

        $this->purchaseDate = $purchaseDate;
        $this->vaccinationDate = $vaccinationDate;
        $this->plantingDate = $plantingDate;
        $this->manufacturer = $manufacturer;
        $this->price = $price;
        $this->soil = $soil;

        $this->oid = OId::next();
        $this->qrCodeBase64 = 'data:image/png;base64,' . base64_encode($this->oid);
    }

    public function changeFields(
        string $title,
        string $room,
        bool $isShown = true,
        ?Attachment $attachment = null,
        ?string $description = null,
        ?DateTime $purchaseDate = null,
        ?DateTime $vaccinationDate = null,
        ?DateTime $plantingDate = null,
        ?string $manufacturer = null,
        ?Price $price = null,
        ?string $soil = null
    ): void
    {
        if ($this->getDeletedAt() !== null) {
            throw new \LogicException('Cannot modify a deleted plant.');
        }

        WebmozartAssert::stringNotEmpty($title);
        $this->title = $title;
        WebmozartAssert::stringNotEmpty($room);
        $this->room = $room;

        $this->isShown = $isShown;
        if ($attachment) {
            $attachment->setAttachableType(Plant::class);
            $attachment->setAttachableId($this->getId());
        }

        $this->attachment = $attachment;
        $this->description = $description;

        $this->purchaseDate = $purchaseDate;
        $this->vaccinationDate = $vaccinationDate;
        $this->plantingDate = $plantingDate;
        $this->manufacturer = $manufacturer;
        $this->price = $price;
        $this->soil = $soil;

        $this->oid = OId::next();
        $this->qrCodeBase64 = 'data:image/png;base64,' . base64_encode($this->oid);
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

//    /**
//     * @return Collection<int,Subscription>
//     */
//    public function getAttachments(): Collection
//    {
//        return $this->attachment;
//    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'oid' => $this->oid,
            'title' => $this->title,
            'is_shown' => $this->isShown,
            'description' => $this->description,
            'qr_code_base64' => $this->qrCodeBase64,
            'room' => $this->room,
            'purchase_date' => $this->purchaseDate,
            'vaccination_date' => $this->vaccinationDate,
            'planting_date' => $this->plantingDate,
            'manufacturer' => $this->manufacturer,
            'price' => $this->price,
            'soil' => $this->soil,
//            'attachment' => array_map(
//                static fn (Attachment $attachment) => $attachment->toArray(),
//                $this->getAttachments()->toArray()
//            ),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
            ];
    }
}
