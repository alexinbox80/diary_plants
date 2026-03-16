<?php //растение

namespace App\Domain\Entity;

use App\Domain\ValueObject\OId;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\ValueObject\Plant\LifeCycle;
use App\Domain\ValueObject\Plant\SalesInfo;
use Doctrine\Common\Collections\Collection;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use App\Domain\ValueObject\Plant\PurchaseInfo;
use Webmozart\Assert\Assert as WebmozartAssert;
use Doctrine\Common\Collections\ArrayCollection;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\ValueObject\Plant\PlantIdentifier;
use App\Domain\Entity\Interfaces\AttachableInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;

#[ORM\Table(name: 'plant')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'plant__oid__ind', columns: ['oid'])]
#[ORM\UniqueConstraint(name: 'plant__oid__uniq', columns: ['oid'], options: ['where' => '(deleted_at IS NULL)'])]
class Plant implements EntityInterface, AttachableInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    //название растение
    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: false)]
    private string $title;

    //описание растения
    #[ORM\Column(name: 'description', type: 'string', length: 1024, nullable: true)]
    private ?string $description = null;

    //помещение
    #[ORM\Column(name: 'room', type: 'string', length: 64, nullable: false)]
    private string $room;

    //показывать растение
    #[ORM\Column(name: 'is_shown', type: 'boolean', options: ['default' => true])]
    private bool $isShown = true;

    //комментарии к растению
    #[ORM\Column(name: 'comment', type: 'string', length: 1024, nullable: true)]
    private ?string $comment = null;

    //UUIDv4 и ссылка на qr code
    #[ORM\Embedded(class: PlantIdentifier::class, columnPrefix: false)]
    private PlantIdentifier $plantIdentifier;

    //стоимость, стоимость доставки, стоимость упаковки, продавец, питомник, дата покупки
    #[ORM\Embedded(class: PurchaseInfo::class, columnPrefix: false)]
    private ?PurchaseInfo $purchaseInfo;

    //дата прививки, дата посадки, описание грунта
    #[ORM\Embedded(class: LifeCycle::class, columnPrefix: false)]
    private ?LifeCycle $lifeCycle;

    //Дата продажи, Стоимость продажи, Продано
    #[ORM\Embedded(class: SalesInfo::class, columnPrefix: false)]
    private ?SalesInfo $salesInfo;

    //связь с использованием удобрений, стимуляторов и обнаруженными вредителями
    #[ORM\OneToMany(targetEntity: Usage::class, mappedBy: 'plant')]
    private Collection $usages;

    //связь с плодами
    #[ORM\OneToMany(targetEntity: Offspring::class, mappedBy: 'plant')]
    private Collection $offsprings;

    //загруженные в репозитории связанные вложения
    private array $loadedAttachments = [];

    //идентификатор связанной сущности group
    #[ORM\ManyToOne(targetEntity: Group::class, cascade: ['all'], inversedBy: 'plants')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

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

    public function __construct(
        Group $group,
        string $title,
        string $room
    )
    {
        $this->setGroupValidate($group);
        $this->setTitleValidate($title);
        $this->setRoomValidate($room);

        $this->plantIdentifier = new PlantIdentifier(OId::next());
        $this->purchaseInfo = new PurchaseInfo();
        $this->lifeCycle = new LifeCycle();
        $this->salesInfo = new SalesInfo();

        $this->offsprings = new ArrayCollection();
        $this->usages = new ArrayCollection();
    }

    public function moveToGroup(Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function isShown(): bool
    {
        return $this->isShown;
    }

    public function show(): void
    {
        $this->isShown = true;
    }

    public function hide(): void
    {
        $this->isShown = false;
    }

    public function changePlantIdentifier(PlantIdentifier $plantIdentifier): self
    {
        $this->plantIdentifier = $plantIdentifier;

        return $this;
    }

    public function changeLifeCycle(LifeCycle $lifeCycle): self
    {
        $this->lifeCycle = $lifeCycle;

        return $this;
    }

    public function changePurchaseInfo(PurchaseInfo $purchaseInfo): self
    {
        $this->purchaseInfo = $purchaseInfo;

        return $this;
    }

    public function changeSalesInfo(SalesInfo $salesInfo): self
    {
        $this->salesInfo = $salesInfo;

        return $this;
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getRoom(): string
    {
        return $this->room;
    }

    public function setRoom(string $room): self
    {
        $this->room = $room;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getPlantIdentifier(): PlantIdentifier
    {
        return $this->plantIdentifier;
    }

    public function getLifeCycle(): LifeCycle
    {
        return $this->lifeCycle;
    }

    public function getPurchaseInfo(): PurchaseInfo
    {
        return $this->purchaseInfo;
    }

    public function getSalesInfo(): SalesInfo
    {
        return $this->salesInfo;
    }

    public function getOffsprings(): Collection
    {
        return $this->offsprings;
    }

    public function getUsages(): Collection
    {
        return $this->usages;
    }

    public function setLoadedAttachments(array $attachments): void
    {
        $this->loadedAttachments = $attachments;
    }

    public function getLoadedAttachments(): array
    {
        return $this->loadedAttachments;
    }
}
