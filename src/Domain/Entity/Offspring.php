<?php //плод

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use App\Domain\ValueObject\Offspring\Phenology;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\ValueObject\Offspring\FruitMetrics;
use App\Domain\Entity\Interfaces\AttachableInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;

#[ORM\Table(name: 'offspring')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'offspring__plant_id__ind', columns: ['plant_id'])]
#[ORM\Index(name: 'offspring__group_id__ind', columns: ['group_id'])]
class Offspring implements EntityInterface, AttachableInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    //дата сбора, дата цветения
    #[ORM\Embedded(class: Phenology::class, columnPrefix: false)]
    private Phenology $phenology;

    //масса гр, цвет, вкус, количество
    #[ORM\Embedded(class: FruitMetrics::class, columnPrefix: false)]
    private FruitMetrics $metrics;

    //комментарии к плоду
    #[ORM\Column(name: 'comment', type: 'string', length: 1024, nullable: true)]
    private ?string $comment = null;

    //идентификатор растения
    #[ORM\ManyToOne(targetEntity: Plant::class, inversedBy: 'offsprings')]
    #[ORM\JoinColumn(name: 'plant_id', referencedColumnName: 'id')]
    private Plant $plant;

    //идентификатор связанной сущности group
    #[ORM\ManyToOne(targetEntity: Group::class, cascade: ['all'], inversedBy: 'offsprings')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

    //загруженные в репозитории связанные вложения
    private array $loadedAttachments = [];

    private function setGroupValidate(Group $group): void
    {
        $this->group = $group;
    }

    private function setPlantValidate(Plant $plant): void
    {
        $this->plant = $plant;
    }

    private function setComment(?string $comment = null): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function __construct(
        Group $group,
        Plant $plant
    ) {
        $this->setGroupValidate($group);
        $this->setPlantValidate($plant);

        $this->phenology = new Phenology();
        $this->metrics = new FruitMetrics();
    }

    public function recordResult(Phenology $phenology, FruitMetrics $metrics, ?string $comment = null): void
    {
        $this->phenology = $phenology;
        $this->metrics = $metrics;
        $this->comment = $comment;
    }

    public function moveToGroup(Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function moveToPlant(Plant $plant): self
    {
        $this->plant = $plant;

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

    public function getPlant(): Plant
    {
        return $this->plant;
    }

    public function getPhenology(): Phenology
    {
        return $this->phenology;
    }

    public function getFruitMetrics(): FruitMetrics
    {
        return $this->metrics;
    }

    public function getComment(): ?string
    {
        return $this->comment;
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
