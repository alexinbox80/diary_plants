<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert as WebmozartAssert;

#[ORM\Table(name: 'usage')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'usage__usable__ind', columns: ['usable_type', 'usable_id'])]
class Usage implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'use_date', type: 'datetimetz', nullable: false)]
    private DateTime $useDate;

    #[ORM\Column(type: 'text', length: 1024, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(name: 'usable_id', type: 'bigint', nullable: true)]
    private ?int $usableId = null;

    #[ORM\Column(name: 'usable_type', type: 'string', nullable: true)]
    private ?string $usableType = null;

    //идентификатор растения
    #[ORM\ManyToOne(targetEntity: Plant::class, inversedBy: 'usages')]
    #[ORM\JoinColumn(name: 'plant_id', referencedColumnName: 'id')]
    private Plant $plant;

    public function setUsableType(?string $usableType = null): void
    {
        $this->usableType = $usableType;
    }

    public function setUsableId(?int $usableId = null): void
    {
        $this->usableId = $usableId;
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));
        return $this->id;
    }

    public function getUseDate(): DateTime
    {
        return $this->useDate;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getUsableId(): ?int
    {
        return $this->usableId;
    }

    public function getUsableType(): ?string
    {
        return $this->usableType;
    }

    public function getPlant(): Plant
    {
        return $this->plant;
    }

    private function setCommonFields(
        DateTime $useDate,
        Plant $plant,
        ?string $comment = null,
        ?int $usableId = null,
        ?string $usableType = null,
    ): void
    {
        $this->setUseDateValidate($useDate);
        $this->setPlantValidate($plant);
        $this->setCommentValidate($comment);
        $this->setUsableIdValidate($usableId);
        $this->setUsableTypeValidate($usableType);
    }

    private function setUseDateValidate(DateTime $useDate): void
    {
        WebmozartAssert::notNull($useDate, 'Use date must not be null.');
        WebmozartAssert::isInstanceOf($useDate, DateTime::class, 'Use date must be a DateTime instance');
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

    private function setUsableIdValidate(?int $usableId = null): void
    {
        $this->usableId = $usableId;
    }

    private function setUsableTypeValidate(?string $usableType = null): void
    {
        $this->usableType = $usableType;
    }

    public function __construct(
        DateTime $useDate,
        Plant $plant,
        ?string $comment = null,
        ?int $usableId = null,
        ?string $usableType = null,
    ) {
        $this->setCommonFields($useDate, $plant, $comment, $usableId, $usableType);
    }

    public function changeFields(
        DateTime $useDate,
        Plant $plant,
        ?string $comment = null,
        ?int $usableId = null,
        ?string $usableType = null,
    ):void
    {
        $this->setCommonFields($useDate, $plant, $comment, $usableId, $usableType);
    }

    public  function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'plant' => $this->getPlant()->toArray(),
            'use_date' => $this->getUseDate()->format('Y-m-d'),
            'comment' => $this->getComment(),
            'usable_id' => $this->getUsableId(),
            'usable_type' => $this->getUsableType(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
