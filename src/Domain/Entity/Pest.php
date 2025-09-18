<?php //вредители

namespace App\Domain\Entity;

use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Webmozart\Assert\Assert as WebmozartAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pest')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'pest__plant_id__ind', columns: ['plant_id'])]
class Pest extends Preparation implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Plant::class, inversedBy: 'pests')]
    #[ORM\JoinColumn(name: 'plant_id', referencedColumnName: 'id')]
    private Plant $plant;

    /**
     * @var Collection<int, Usage>
     */
    #[ORM\OneToMany(targetEntity: Usage::class, mappedBy: 'usage', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'usable_id', nullable: true)]
    private Collection $usages;


    public function __construct(
        Plant    $plant,
        string   $title,
        int      $quantity,
        DateTime $useDate,
        ?string  $manufacturer = null,
        ?string  $description = null,
        ?string  $comment = null
    )
    {
        parent::__construct($title, $quantity, $useDate, $manufacturer, $description, $comment);

        $this->plant = $plant;
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    public function getPlant(): Plant
    {
        return $this->plant;
    }

    public function changeFieldsWithPlant(
        string   $title,
        int      $quantity,
        DateTime $useDate,
        Plant    $plant,
        ?string  $manufacturer = null,
        ?string  $description = null,
        ?string  $comment = null,
    ): void
    {
        parent::changeFields($title, $quantity, $useDate, $manufacturer, $description, $comment);
        $this->plant = $plant;
    }

    public function getUsages(): Collection
    {
        return $this->usages;
    }

    public function addUsage(Usage $usage): self
    {
        if (!$this->usages->contains($usage)) {
            $this->usages[] = $usage;
            $usage->setUsableType(self::class); // Set the type
            $usage->setUsableId($this->getId()); // Set the ID
        }
        return $this;
    }

    public function removeUsage(Usage $usage): self
    {
        $this->usages->removeElement($usage);
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'id' => $this->getId(),
                'plant' => $this->getPlant()->toArray(),
                'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
                'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
            ]
        );
    }
}
