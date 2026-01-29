<?php //плод

namespace App\Domain\Entity;

use DateTimeImmutable;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\AttachableInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Webmozart\Assert\Assert as WebmozartAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'offspring')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'offspring__plant_id__ind', columns: ['plant_id'])]
class Offspring implements EntityInterface, AttachableInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    //дата сбора
    #[ORM\Column(name: 'fruiting_date', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $fruitingDate = null;

    //дата цветения
    #[ORM\Column(name: 'flowering_date', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $floweringDate = null;

    //масса гр
    #[ORM\Column(name: 'mass', type: 'integer', nullable: true)]
    private ?int $mass = null;

    //цвет
    #[ORM\Column(name: 'color', type: 'string', length: 64, nullable: true)]
    private ?string $color = null;

    //вкус
    #[ORM\Column(name: 'flavor', type: 'string', length: 64, nullable: true)]
    private ?string $flavor = null;

    //количество
    #[ORM\Column(name: 'quantity', type: 'integer', nullable: true)]
    private ?int $quantity = null;

    //комментарии к плоду
    #[ORM\Column(name: 'comment', type: 'string', length: 1024, nullable: true)]
    private ?string $comment = null;

    //идентификатор растения
    #[ORM\ManyToOne(targetEntity: Plant::class, inversedBy: 'offsprings')]
    #[ORM\JoinColumn(name: 'plant_id', referencedColumnName: 'id')]
    private Plant $plant;

    /**
     * @var Collection<int, Attachment>
     */
    //связь с файлом
    #[ORM\OneToMany(targetEntity: Attachment::class, mappedBy: 'attachable', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'attachable_id', nullable: true)]
    private Collection $attachments;

    private function setCommonFields(
        Plant $plant,
        ?DateTimeImmutable $fruitingDate = null,
        ?DateTimeImmutable $floweringDate = null,
        ?int $mass = null,
        ?string $color = null,
        ?string $flavor = null,
        ?int $quantity = null,
        ?string $comment = null,
    ): void {
        $this->setPlantValidate($plant);
        $this->setFruitingDateValidate($fruitingDate);
        $this->setFloweringDateValidate($floweringDate);
        $this->setMassValidate($mass);
        $this->setColorValidate($color);
        $this->setFlavorValidate($flavor);
        $this->setQuantityValidate($quantity);
        $this->setCommentValidate($comment);
    }

    private function setPlantValidate(Plant $plant): void
    {
        $this->plant = $plant;
    }

    private function setFruitingDateValidate(?DateTimeImmutable $fruitingDate = null): void
    {
        if ($fruitingDate !== null) {
            WebmozartAssert::isInstanceOf($fruitingDate, DateTimeImmutable::class, 'Use date must be a DateTime instance');
            $this->fruitingDate = $fruitingDate;
        }
    }

    private function setFloweringDateValidate(?DateTimeImmutable $floweringDate = null): void
    {
        if ($floweringDate !== null) {
            WebmozartAssert::isInstanceOf($floweringDate, DateTimeImmutable::class, 'Use date must be a DateTime instance');
            $this->floweringDate = $floweringDate;
        }
    }

    private function setMassValidate(?int $mass = null): void
    {
        $this->mass = $mass;
    }

    private function setColorValidate(?string $color = null): void
    {
        $this->color = $color;
    }

    private function setFlavorValidate(?string $flavor = null): void
    {
        $this->flavor = $flavor;
    }

    private function setQuantityValidate(?int $quantity = null): void
    {
        $this->quantity = $quantity;
    }

    private function setCommentValidate(?string $comment = null): void
    {
        $this->comment = $comment;
    }

    public function __construct(
        Plant $plant,
        ?DateTimeImmutable $fruitingDate = null,
        ?DateTimeImmutable $floweringDate = null,
        ?int $mass = null,
        ?string $color = null,
        ?string $flavor = null,
        ?int $quantity = null,
        ?string $comment = null,
    ) {

        $this->attachments = new ArrayCollection();

        $this->setCommonFields(
            $plant,
            $fruitingDate,
            $floweringDate,
            $mass,
            $color,
            $flavor,
            $quantity,
            $comment
        );
    }

    public function changeFields(
        Plant $plant,
        ?DateTimeImmutable $fruitingDate = null,
        ?DateTimeImmutable $floweringDate = null,
        ?int $mass = null,
        ?string $color = null,
        ?string $flavor = null,
        ?int $quantity = null,
        ?string $comment = null,
    ): void
    {
        $this->setCommonFields(
            $plant,
            $fruitingDate,
            $floweringDate,
            $mass,
            $color,
            $flavor,
            $quantity,
            $comment
        );
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    /**
     * @return Collection<int, Attachment>
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function getFruitingDate(): ?DateTimeImmutable
    {
        return $this->fruitingDate;
    }

    public function getFloweringDate(): ?DateTimeImmutable
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getPlant(): Plant
    {
        return $this->plant;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'attachment' => array_map(
                static fn (Attachment $attachment) => $attachment->toArray(),
                $this->getAttachments()->toArray()
            ),
            'plant' => $this->getPlant()->toArray(),
            'fruiting_date' => $this->getFruitingDate(),
            'flowering_date' => $this->getFloweringDate(),
            'mass' => $this->getMass(),
            'color' => $this->getColor(),
            'flavor' => $this->getFlavor(),
            'quantity' => $this->getQuantity(),
            'comment' => $this->getComment(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
