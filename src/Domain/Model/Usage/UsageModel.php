<?php

namespace App\Domain\Model\Usage;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Usage;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Model\Group\GroupModel;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use App\Domain\Model\Interfaces\AttachableModelInterface;
class UsageModel
{
    public function __construct(
        private readonly int $id,
        private readonly int $groupId,
        private readonly ?GroupModel $group = null,
        private readonly DateTimeImmutable $useDate,
        private readonly int $plantId,
        private readonly ?PlantModel $plant = null,
        private readonly ?string $comment = null,
        private readonly ?int $usableId = null,
        private readonly ?string $usableType = null,
        private readonly ?AttachableModelInterface $attachable = null,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getGroup(): ?GroupModel
    {
        return $this->group;
    }

    public function getUseDate(): DateTimeImmutable
    {
        return $this->useDate;
    }

    public function getPlantId(): int
    {
        return $this->plantId;
    }

    public function getPlant(): ?PlantModel
    {
        return $this->plant;
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

    public function getAttachable(): ?AttachableModelInterface
    {
        return $this->attachable;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param Usage $usage
     * @param GroupModel|null $groupModel
     * @param PlantModel|null $plantModel
     * @param AttachableModelInterface|null $attachableModel
     * @return UsageModel
     */
    static function fromEntity(Usage $usage, ?GroupModel $groupModel = null, ?PlantModel $plantModel = null, ?AttachableModelInterface $attachableModel = null): self
    {
        return new self(
            $usage->getId(),
            $usage->getGroup()->getId(),
            $groupModel,
            $usage->getUseDate(),
            $usage->getPlant()->getId(),
            $plantModel,
            $usage->getComment(),
            $usage->getTarget()->getUsableId(),
            $usage->getTarget()->getUsableType()->value,
            $attachableModel,
            $usage->getCreatedAt(),
            $usage->getUpdatedAt()
        );
    }

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => '#',
            'group_id' => 'Идентификатор группы',
            'group_title' => 'Группа',
            'use_date' => 'Дата использования',
            'plant_id' => 'Идентификатор растения',
            'plant_title' => 'Растение',
            'usable_id' => 'ID сущности',
            'usable_type' => 'Тип сущности',
            'comment' => 'Коментарии',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления'
        ];
    }

    public function toArray(): array
    {
        $timezone = new DateTimeZone('Europe/Moscow');

        return [
            'id' => $this->getId(),
            'group_id' => $this->getGroupId(),
            'group_title' => $this->getGroup()?->getTitle(),
            'use_date' => $this->getUseDate()->setTimezone($timezone)->format('d.m.Y'),
            'plant_id' => $this->getPlantId(),
            'plant_title' => $this->getPlant()->getTitle(),
            'usable_id' => $this->getUsableId(),
            'usable_type' => AttachableType::getLabel($this->getUsableType()),
            'comment' => $this->getComment(),
            'attachable' => $this->getAttachable(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s')
        ];
    }
}
