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
    #[ORM\Column(type: 'oid', nullable: true)]
    private ?OId $oid = null;

    //название растение
    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: false)]
    private string $title;

    //описание растения
    #[ORM\Column(name: 'description', type: 'string', length: 1024, nullable: true)]
    private ?string $description = null;

    //ссылка на файл с qr кодом
    #[ORM\Column(name: 'qr_code_link', type: 'string', length: 255, nullable: true)]
    private ?string $qrCodeLink = null;

    //помещение
    #[ORM\Column(name: 'room', type: 'string', length: 64, nullable: false)]
    private string $room;

    //дата покупки
    #[ORM\Column(name: 'purchase_date', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $purchaseDate = null;

    //дата прививки
    #[ORM\Column(name: 'vaccination_date', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $vaccinationDate = null;

    //дата посадки
    #[ORM\Column(name: 'planting_date', type: 'datetimetz_immutable', nullable: true)]
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

    //Продано
    #[ORM\Column(name: 'is_sold', type: 'boolean', options: ['default' => false])]
    private bool $isSold = false;

    //Дата продажи
    #[ORM\Column(name: 'selling_date', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $sellingDate = null;

    //Стоимость продажи
    #[ORM\Column(type: 'price', length:10, nullable: true)]
    private ?Price $sellingPrice = null;

    //связь с задачами
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'plant')]
    private Collection $tasks;

    //связь с использованием удобрений, стимуляторов и обнаруженными вредителями
    #[ORM\OneToMany(targetEntity: Usage::class, mappedBy: 'plant')]
    private Collection $usages;

    //связь с плодами
    #[ORM\OneToMany(targetEntity: Offspring::class, mappedBy: 'plant')]
    private Collection $offsprings;

    //связь с удобрениями
    #[ORM\OneToMany(targetEntity: Fertilizer::class, mappedBy: 'plant')]
    private Collection $fertilizers;

    //идентификатор связанной сущности group
    #[ORM\ManyToOne(targetEntity: Group::class, cascade: ['all'], fetch: 'EAGER', inversedBy: 'plants')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

    private function setCommonFields(
        Group $group,
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
        bool $isSold = false,
        ?DateTimeImmutable $sellingDate = null,
        ?Price $sellingPrice = null,
        ?string $comment = null,
    ): void {
        $this->setGroupValidate($group);
        $this->setTitleValidate($title);
        $this->setRoomValidate($room);
        $this->setIsShownValidate($isShown);
        $this->setDescriptionValidate($description);
        $this->setPurchaseDateValidate($purchaseDate);
        $this->setVaccinationDateValidate($vaccinationDate);
        $this->setPlantingDateValidate($plantingDate);
        $this->setSellerValidate($seller);
        $this->setNurseryValidate($nursery);
        $this->setPriceValidate($price);
        $this->setShippingCostValidate($shippingCost);
        $this->setPackagingCostValidate($packagingCost);
        $this->setSoilValidate($soil);
        $this->setIsSoldValidate($isSold);
        $this->setSellingDateValidate($sellingDate);
        $this->setSellingPriceValidate($sellingPrice);
        $this->setCommentValidate($comment);
    }

    private function setGroupValidate(Group $group): void
    {
        $this->group = $group;
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

    private function setDescriptionValidate(?string $description = null): void
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

    private function setIsSoldValidate(bool $isSold = false): void
    {
        $this->isSold = $isSold;
    }

    private function setSellingDateValidate(?DateTimeImmutable $sellingDate = null): void
    {
        $this->sellingDate = $sellingDate;
    }

    private function setSellingPriceValidate(?Price $sellingPrice = null): void
    {
        $this->sellingPrice = $sellingPrice;
    }

    private function setCommentValidate(?string $comment = null): void
    {
        $this->comment = $comment;
    }

    public function __construct(
        Group $group,
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
        bool $isSold = false,
        ?DateTimeImmutable $sellingDate = null,
        ?Price $sellingPrice = null,
        ?string $comment = null,
    )
    {
        $this->setCommonFields(
            $group,
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
            $isSold,
            $sellingDate,
            $sellingPrice,
            $comment,
        );

        $this->oid = OId::next();

        $this->tasks = new ArrayCollection();
        $this->offsprings = new ArrayCollection();
        $this->fertilizers = new ArrayCollection();
        $this->usages = new ArrayCollection();
    }

    public function changeFields(
        Group $group,
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
        bool $isSold = false,
        ?DateTimeImmutable $sellingDate = null,
        ?Price $sellingPrice = null,
        ?string $comment = null
    ): void
    {
        if ($this->getDeletedAt() !== null) {
            throw new \LogicException('Cannot modify a deleted plant.');
        }

        $this->setCommonFields(
            $group,
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
            $isSold,
            $sellingDate,
            $sellingPrice,
            $comment,
        );

        $this->oid = OId::next();
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

    public function getQrCodeLink(): ?string
    {
        return $this->qrCodeLink;
    }

    public function setQrCodeLink(string $link): self
    {
        $this->qrCodeLink = $link;

        return $this;
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

    public function isSold(): bool
    {
        return $this->isSold;
    }

    public function getSellingDate(): ?DateTimeImmutable
    {
        return $this->sellingDate;
    }

    public function getSellingPrice(): ?Price
    {
        return $this->sellingPrice;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
