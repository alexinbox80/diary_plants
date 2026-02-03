<?php //растение

namespace App\Domain\Entity;

use App\Domain\Entity\Interfaces\AttachableInterface;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use App\Domain\ValueObject\OId;
use App\Domain\ValueObject\Price;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert as WebmozartAssert;

#[ORM\Table(name: 'plant')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'plant__oid__ind', columns: ['oid'])]
#[ORM\UniqueConstraint(name: 'plant__oid__uniq', fields: ['oid'], options: ['where' => '(deleted_at IS NULL)'])]
class Plant implements EntityInterface, AttachableInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    //UUIDv4
    #[ORM\Column(type: 'oid', unique: true, nullable: true)]
    private ?OId $oid = null;

    //название растение
    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: false)]
    private string $title;

    //описание растения
    #[ORM\Column(name: 'description', type: 'string', length: 1024, nullable: true)]
    private ?string $description = null;

    //qr код
    #[ORM\Column(name: 'qr_code_base64', type: 'string', length: 94, nullable: true)]
    private ?string $qrCodeBase64 = null;

    //помещение
    #[ORM\Column(name: 'room', type: 'string', length: 64, nullable: false)]
    private string $room;

    //дата покупки
    #[ORM\Column(name: 'purchase_date', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $purchaseDate = null;

    //дата прививки
    #[ORM\Column(name: 'vaccination_date', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $vaccinationDate = null;

    //дата посадки
    #[ORM\Column(name: 'planting_date', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $plantingDate = null;

    //продавец
    #[ORM\Column(name: 'seller', type: 'string', length: 255, nullable: true)]
    private ?string $seller = null;

    //питомник
    #[ORM\Column(name: 'nursery', type: 'string', length: 255, nullable: true)]
    private ?string $nursery = null;

    //стоимость
    #[ORM\Column(type: 'price', length:10, nullable: true)]
    private ?Price $price = null;

    //стоимость доставки
    #[ORM\Column(type: 'price', length:10, nullable: true)]
    private ?Price $shippingCost = null;

    //стоимость упаковки
    #[ORM\Column(type: 'price', length:10, nullable: true)]
    private ?Price $packagingCost = null;

    //показывать растение
    #[ORM\Column(name: 'is_shown', type: 'boolean', options: ['default' => true])]
    private bool $isShown = true;

    //описание грунта
    #[ORM\Column(name: 'soil', type: 'string', length: 255, nullable: true)]
    private ?string $soil = null;

    //комментарии к растению
    #[ORM\Column(name: 'comment', type: 'string', length: 1024, nullable: true)]
    private ?string $comment = null;

    //связь с задачами
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'plant')]
    private Collection $tasks;

    //связь с использованием удобрений, стимуляторов и обнаруженными вредителями
    #[ORM\OneToMany(targetEntity: Usage::class, mappedBy: 'plant')]
    private Collection $usages;

    //связь с плодами
    #[ORM\OneToMany(targetEntity: Offspring::class, mappedBy: 'plant')]
    private Collection $offsprings;

    /**
     * @var Collection<int, Attachment>
     */
    #[ORM\OneToMany(targetEntity: Attachment::class, mappedBy: 'attachable', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'attachable_id', nullable: true)]
    private Collection $attachments;

    private function setCommonFields(
        string $title,
        string $room,
        bool $isShown = true,
        ?string $description = null,
        ?DateTimeImmutable $purchaseDate = null,
        ?DateTimeImmutable $vaccinationDate = null,
        ?DateTimeImmutable $plantingDate = null,
        ?string $seller = null,
        ?string $nursery = null,
        ?Price $price = null,
        ?Price $shippingCost = null,
        ?Price $packagingCost = null,
        ?string $soil = null,
        ?string $comment = null,
    ): void {
        $this->setTitleValidate($title);
        $this->setRoomValidate($room);
        $this->setIsShownValidate($isShown);
        $this->setDesscriptionValidate($description);
        $this->setPurchaseDateValidate($purchaseDate);
        $this->setVaccinationDateValidate($vaccinationDate);
        $this->setPlantingDateValidate($plantingDate);
        $this->setSellerValidate($seller);
        $this->setNurseryValidate($nursery);
        $this->setPriceValidate($price);
        $this->setShippingCostValidate($shippingCost);
        $this->setPackagingCostValidate($packagingCost);
        $this->setSoilValidate($soil);
        $this->setCommentValidate($comment);
    }

    private function setTitleValidate(string $title): void
    {
        WebmozartAssert::stringNotEmpty($title, 'Title should not be empty. Got: %s');
        WebmozartAssert::lengthBetween($title, 2, 255, 'Title must be a string valid length of 2-255 letters. Got: %s');

        $this->title = $title;
    }

    private function setRoomValidate(string $room): void
    {
        WebmozartAssert::stringNotEmpty($room, 'Room should not be empty. Got: %s');
        WebmozartAssert::lengthBetween($room, 2, 64, 'Room must be a string valid length of 2-255 letters. Got: %s');

        $this->room = $room;
    }

    private function setIsShownValidate(bool $isShown): void
    {
        $this->isShown = $isShown;
    }

    private function setDesscriptionValidate(?string $description = null): void
    {
        $this->description = $description;
    }

    private function setPurchaseDateValidate(?DateTimeImmutable $purchaseDate = null): void
    {
        $this->purchaseDate = $purchaseDate;
    }

    private function setVaccinationDateValidate(?DateTimeImmutable $vaccinationDate = null): void
    {
        $this->vaccinationDate = $vaccinationDate;
    }

    private function setPlantingDateValidate(?DateTimeImmutable $plantingDate = null): void
    {
        $this->plantingDate = $plantingDate;
    }

    private function setSellerValidate(?string $seller = null): void
    {
        $this->seller = $seller;
    }

    private function setNurseryValidate(?string $nursery = null): void
    {
        $this->nursery = $nursery;
    }

    private function setPriceValidate(?Price $price = null): void
    {
        $this->price = $price;
    }

    private function setShippingCostValidate(?Price $shippingCost = null): void
    {
        $this->shippingCost = $shippingCost;
    }

    private function setPackagingCostValidate(?Price $packagingCost = null): void
    {
        $this->packagingCost = $packagingCost;
    }

    private function setSoilValidate(?string $soil = null): void
    {
        $this->soil = $soil;
    }

    private function setCommentValidate(?string $comment = null): void
    {
        $this->comment = $comment;
    }

    public function __construct(
        string $title,
        string $room,
        bool $isShown = true,
        ?string $description = null,
        ?DateTimeImmutable $purchaseDate = null,
        ?DateTimeImmutable $vaccinationDate = null,
        ?DateTimeImmutable $plantingDate = null,
        ?string $seller = null,
        ?string $nursery = null,
        ?Price $price = null,
        ?Price $shippingCost = null,
        ?Price $packagingCost = null,
        ?string $soil = null,
        ?string $comment = null,
    )
    {
        $this->setCommonFields(
            $title,
            $room,
            $isShown,
            $description,
            $purchaseDate,
            $vaccinationDate,
            $plantingDate,
            $seller,
            $nursery,
            $price,
            $shippingCost,
            $packagingCost,
            $soil,
            $comment,
        );

        $this->oid = OId::next();
        $this->qrCodeBase64 = 'data:image/png;base64,' . base64_encode($this->oid);

        $this->attachments = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->offsprings = new ArrayCollection();
        $this->usages = new ArrayCollection();
    }

    public function changeFields(
        string $title,
        string $room,
        bool $isShown = true,
        ?string $description = null,
        ?DateTimeImmutable $purchaseDate = null,
        ?DateTimeImmutable $vaccinationDate = null,
        ?DateTimeImmutable $plantingDate = null,
        ?string $seller = null,
        ?string $nursery = null,
        ?Price $price = null,
        ?Price $shippingCost = null,
        ?Price $packagingCost = null,
        ?string $soil = null,
        ?string $comment = null
    ): void
    {
        if ($this->getDeletedAt() !== null) {
            throw new \LogicException('Cannot modify a deleted plant.');
        }

        $this->setCommonFields(
            $title,
            $room,
            $isShown,
            $description,
            $purchaseDate,
            $vaccinationDate,
            $plantingDate,
            $seller,
            $nursery,
            $price,
            $shippingCost,
            $packagingCost,
            $soil,
            $comment,
        );

        $this->oid = OId::next();
        $this->qrCodeBase64 = 'data:image/png;base64,' . base64_encode($this->oid);
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
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

    public function getPurchaseDate(): ?DateTimeImmutable
    {
        return $this->purchaseDate;
    }

    public function getVaccinationDate(): ?DateTimeImmutable
    {
        return $this->vaccinationDate;
    }

    public function getPlantingDate(): ?DateTimeImmutable
    {
        return $this->plantingDate;
    }

    public function getSeller(): ?string
    {
        return $this->seller;
    }

    public function getNursery(): ?string
    {
        return $this->nursery;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function getShippingCost(): ?Price
    {
        return $this->shippingCost;
    }

    public function getPackagingCost(): ?Price
    {
        return $this->packagingCost;
    }

    public function isShown(): bool
    {
        return $this->isShown;
    }

    public function getSoil(): ?string
    {
        return $this->soil;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'oid' => $this->getOid(),
            'title' => $this->getTitle(),
            'is_shown' => $this->isShown(),
            'description' => $this->getDescription(),
            'qr_code_base64' => $this->getQrCodeBase64(),
            'room' => $this->getRoom(),
            'purchase_date' => $this->getPurchaseDate(),
            'vaccination_date' => $this->getVaccinationDate(),
            'planting_date' => $this->getPlantingDate(),
            'seller' => $this->getSeller(),
            'nursery' => $this->getNursery(),
            'price' => $this->getPrice(),
            'shipping_cost' => $this->getShippingCost(),
            'packaging_cost' => $this->getPackagingCost(),
            'soil' => $this->getSoil(),
            'attachment' => array_map(
                static fn (Attachment $attachment) => $attachment->toArray(),
                $this->getAttachments()->toArray()
            ),
            'comment' => $this->getComment(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
