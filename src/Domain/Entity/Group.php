<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
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

    //связь со статусами
    #[ORM\OneToMany(targetEntity: Status::class, mappedBy: 'group', cascade: ['remove'])]
    private Collection $statuses;

    //связь с задачами
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'group', cascade: ['remove'])]
    private Collection $tasks;

    //связь с использованием
    #[ORM\OneToMany(targetEntity: Usage::class, mappedBy: 'group', cascade: ['remove'])]
    private Collection $usages;

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
        $this->statuses = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->usages = new ArrayCollection();
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
}
