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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Webmozart\Assert\Assert as WebmozartAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'plant')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'plant__oid__ind', columns: ['oid'])]
#[ORM\UniqueConstraint(name: 'plant__oid__uniq', fields: ['oid'], options: ['where' => '(deleted_at IS NULL)'])]
class Plant implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: 'oid', unique: true, nullable: true)]
    private ?OId $oid = null;

    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: false)]
    private string $title;

    #[ORM\Column(name: 'description', type: 'string', length: 1024, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'qr_code_base64', type: 'string', length: 64, nullable: true)]
    private ?string $qrCodeBase64 = null;

    #[ORM\Column(name: 'room', type: 'string', length: 64, nullable: false)]
    private string $room;

    #[ORM\Column(name: 'purchase_date', type: 'datetime', nullable: true)]
    private ?DateTime $purchaseDate = null;

    #[ORM\Column(name: 'vaccination_date', type: 'datetime', nullable: true)]
    private ?DateTime $vaccinationDate = null;

    #[ORM\Column(name: 'planting_date', type: 'datetime', nullable: true)]
    private ?DateTime $plantingDate = null;

    #[ORM\Column(name: 'manufacturer', type: 'string', length: 255, nullable: true)]
    private ?string $manufacturer = null;

    #[ORM\Column(type: 'price', length:10, nullable: true)]
    private ?Price $price = null;

    #[ORM\Column(name: 'is_shown', type: 'boolean', options: ['default' => true])]
    private bool $isShown = true;

    #[ORM\Column(name: 'soil', type: 'string', length: 255, nullable: true)]
    private ?string $soil = null;

    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'plant')]
    private Collection $tasks;

    #[ORM\OneToMany(targetEntity: Offspring::class, mappedBy: 'plant')]
    private Collection $offsprings;

    #[ORM\OneToMany(targetEntity: Fertilizer::class, mappedBy: 'plant')]
    private Collection $fertilizers;

    #[ORM\OneToMany(targetEntity: Pest::class, mappedBy: 'plant')]
    private Collection $pests;

    #[ORM\OneToMany(targetEntity: Stimulant::class, mappedBy: 'plant')]
    private Collection $stimulants;

//    /**
//     * @var Collection<int, Attachment>
//     */
//    #[ORM\OneToMany(targetEntity: Attachment::class, mappedBy: 'attachable', cascade: ['persist', 'remove'])]
//    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'attachable_id', nullable: true)]
//    private Collection $attachments;

    public function __construct(
        string $title,
        string $room,
        bool $isShown = true,
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

        $this->description = $description;

        $this->purchaseDate = $purchaseDate;
        $this->vaccinationDate = $vaccinationDate;
        $this->plantingDate = $plantingDate;
        $this->manufacturer = $manufacturer;
        $this->price = $price;
        $this->soil = $soil;

        $this->oid = OId::next();
        $this->qrCodeBase64 = 'data:image/png;base64,' . base64_encode($this->oid);

//        $this->attachments = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        //$this->offersprings = new ArrayCollection();
        $this->fertilizers = new ArrayCollection();
        $this->pests = new ArrayCollection();
        $this->stimulants = new ArrayCollection();
    }

    public function changeFields(
        string $title,
        string $room,
        bool $isShown = true,
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

    public function addAttachment(Attachment $attachment): self
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments[] = $attachment;
            $attachment->setAttachableType(self::class); // Set the type
            $attachment->setAttachableId($this->getId()); // Set the ID
        }
        return $this;
    }

    public function getFertilizers(): Collection
    {
        return $this->fertilizers;
    }

    public function addFertilizer(Fertilizer $fertilizer): self
    {
        if (!$this->fertilizers->contains($fertilizer)) {
            $this->fertilizers[] = $fertilizer;
            $fertilizer->setPlant($this);
        }

        return $this;
    }

    public function removeFertilizer(Fertilizer $fertilizer): self
    {
        if ($this->fertilizers->removeElement($fertilizer)) {
            // set the owning side to null (unless already changed)
            if ($fertilizer->getPlant() === $this) {
                $fertilizer->setPlant(null);
            }
        }

        return $this;
    }

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
//                $attachments
//            ),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
            ];
    }
}
