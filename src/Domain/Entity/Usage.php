<?php //сущность использования для стимуляторов удобрений и вредителей

namespace App\Domain\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\ValueObject\Usage\AttachableReference;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;

#[ORM\Table(name: 'usage')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'usage__usable__ind', columns: ['usable_type', 'usable_id'])]
#[ORM\Index(name: 'usage__plant_id__ind', columns: ['plant_id'])]
#[ORM\Index(name: 'usage__group_id__ind', columns: ['group_id'])]
class Usage implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    //дата использования
    #[ORM\Column(name: 'use_date', type: 'datetimetz_immutable', nullable: false)]
    private DateTimeImmutable $useDate;

    //комментарий
    #[ORM\Column(type: 'text', length: 1024, nullable: true)]
    private ?string $comment = null;

    //идентификатор связанной сущности, тип связанной сущности
    #[ORM\Embedded(class: AttachableReference::class, columnPrefix: false)]
    private AttachableReference $target;

    //идентификатор растения
    #[ORM\ManyToOne(targetEntity: Plant::class, inversedBy: 'usages')]
    #[ORM\JoinColumn(name: 'plant_id', referencedColumnName: 'id')]
    private Plant $plant;

    //идентификатор связанной сущности group
    #[ORM\ManyToOne(targetEntity: Group::class, cascade: ['all'], fetch: 'EAGER', inversedBy: 'usages')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));
        return $this->id;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function getUseDate(): DateTimeImmutable
    {
        return $this->useDate;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getPlant(): Plant
    {
        return $this->plant;
    }

    public function getTarget(): AttachableReference
    {
        return $this->target;
    }

    private function setCommonFields(
        Group $group,
        DateTimeImmutable $useDate,
        Plant $plant,
        ?string $comment = null,
    ): void
    {
        $this->setGroupValidate($group);
        $this->setUseDateValidate($useDate);
        $this->setPlantValidate($plant);
        $this->setCommentValidate($comment);
    }

    private function setGroupValidate(Group $group): void
    {
        $this->group = $group;
    }

    private function setUseDateValidate(DateTimeImmutable $useDate): void
    {
        WebmozartAssert::notNull($useDate, 'Use date must not be null.');
        WebmozartAssert::isInstanceOf($useDate, DateTimeImmutable::class, 'Use date must be a DateTime instance');
        $this->useDate = $useDate;
    }

    private function setPlantValidate(Plant $plant): void
    {
        $this->plant = $plant;
    }

    private function setCommentValidate(?string $comment = null): void
    {
        $this->comment = $comment;
    }

    public function __construct(
        Group $group,
        DateTimeImmutable $useDate,
        Plant $plant,
        AttachableReference $target,
        ?string $comment = null
    ) {
        $this->setCommonFields($group, $useDate, $plant, $comment);

        $this->target = $target;
    }

    public function changeFields(
        Group $group,
        DateTimeImmutable $useDate,
        Plant $plant,
        AttachableReference $target,
        ?string $comment = null,
    ):void
    {
        $this->setCommonFields($group, $useDate, $plant, $comment);

        $this->target = $target;
    }

    public function moveToGroup(Group $group): self
    {
        $this->group = $group;

        return $this;
    }
}
