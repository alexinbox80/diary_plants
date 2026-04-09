<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use Doctrine\Common\Collections\ArrayCollection;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;

#[ORM\Table(name: '`group`')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Group implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    //заголовок
    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: false)]
    private string $title;

    //описание
    #[ORM\Column(name: 'description', type: 'string', length: 1024, nullable: true)]
    private ?string $description = null;

    //флаг блокировки группы
    #[ORM\Column(name: 'is_active', type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    //связь с вложениями
    #[ORM\OneToMany(targetEntity: Attachment::class, mappedBy: 'group', cascade: ['remove'])]
    private Collection $attachments;

    //связь с растениями
    #[ORM\OneToMany(targetEntity: Plant::class, mappedBy: 'group', cascade: ['remove'])]
    private Collection $plants;

    //связь с плодами
    #[ORM\OneToMany(targetEntity: Offspring::class, mappedBy: 'group', cascade: ['remove'])]
    private Collection $offsprings;

    //связь с удобрениями
    #[ORM\OneToMany(targetEntity: Fertilizer::class, mappedBy: 'group', cascade: ['remove'])]
    private Collection $fertilizers;

    //связь со стимуляторами
    #[ORM\OneToMany(targetEntity: Stimulant::class, mappedBy: 'group', cascade: ['remove'])]
    private Collection $stimulants;

    //связь с вредителями
    #[ORM\OneToMany(targetEntity: Pest::class, mappedBy: 'group', cascade: ['remove'])]
    private Collection $pests;

    //связь с пользователями
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'group', cascade: ['remove'])]
    private Collection $users;

    //связь с обозначением
    #[ORM\OneToMany(targetEntity: Marker::class, mappedBy: 'group', cascade: ['remove'])]
    private Collection $markers;

    //связь с поливами
    #[ORM\OneToMany(targetEntity: Watering::class, mappedBy: 'group', cascade: ['remove'])]
    private Collection $waterings;

    //связь с использованием
    #[ORM\OneToMany(targetEntity: Usage::class, mappedBy: 'group', cascade: ['remove'])]
    private Collection $usages;

    //связь с пересадкой растения
    #[ORM\OneToMany(targetEntity: Repotting::class, mappedBy: 'group', cascade: ['remove'])]
    private Collection $repottings;

    public function __construct(
        bool $isActive,
        string $title,
        ?string $description = null,
    )
    {
        $this->setCommonFields($isActive, $title, $description);

        $this->attachments = new ArrayCollection();
        $this->plants = new ArrayCollection();
        $this->offsprings = new ArrayCollection();
        $this->fertilizers = new ArrayCollection();
        $this->stimulants = new ArrayCollection();
        $this->pests = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->markers = new ArrayCollection();
        $this->waterings = new ArrayCollection();
        $this->usages = new ArrayCollection();
        $this->repottings = new ArrayCollection();
    }

    private function setCommonFields(
        bool $isActive,
        string $title,
        ?string $description = null
    ): void {
        $this->isActive = $isActive;
        $this->title = $title;
        $this->description = $description;
    }

    public function changeFields(
        bool $isActive,
        string $title,
        ?string $description = null
    ): void {
        $this->setCommonFields($isActive, $title, $description);
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getRepottings(): Collection
    {
        return $this->repottings;
    }

    public function getUsages(): Collection
    {
        return $this->usages;
    }

    public function getWaterings(): Collection
    {
        return $this->waterings;
    }

    public function getMarkers(): Collection
    {
        return $this->markers;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function getPests(): Collection
    {
        return $this->pests;
    }

    public function getStimulants(): Collection
    {
        return $this->stimulants;
    }

    public function getFertilizers(): Collection
    {
        return $this->fertilizers;
    }

    public function getOffsprings(): Collection
    {
        return $this->offsprings;
    }

    public function getPlants(): Collection
    {
        return $this->plants;
    }

    public function getAttachments(): Collection
    {
        return $this->attachments;
    }
}
