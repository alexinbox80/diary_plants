<?php

namespace App\Domain\Model\Repotting;

use App\Domain\ValueObject\Enum\Repotting\PotMaterial;
use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Repotting;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\ValueObject\Enum\Repotting\RepottingType;

class RepottingModel
{
    public function __construct(
        private readonly int $id,
        private readonly int $groupId,
        private readonly ?GroupModel $group = null,
        private readonly int $plantId,
        private readonly ?PlantModel $plant = null,
        private readonly DateTimeImmutable $repottedAt,
        private readonly string $type,
        private readonly string $potMaterial,
        private readonly string $potSize,
        private readonly ?string $comment = null,
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

    public function getPlantId(): int
    {
        return $this->plantId;
    }

    public function getPlant(): ?PlantModel
    {
        return $this->plant;
    }

    public function getRepottedAt(): DateTimeImmutable
    {
        return $this->repottedAt;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getPotMaterial(): string
    {
        return $this->potMaterial;
    }

    public function getPotSize(): string
    {
        return $this->potSize;
    }

    public function getComment(): ?string
    {
        return $this->comment;
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
     * @param Repotting $repotting
     * @param GroupModel|null $groupModel
     * @param PlantModel|null $plantModel
     * @return RepottingModel
     */
    public static function fromEntity(Repotting $repotting, ?GroupModel $groupModel = null, ?PlantModel $plantModel = null): self
    {
        return new self(
            $repotting->getId(),
            $repotting->getGroup()->getId(),
            $groupModel,
            $repotting->getPlant()->getId(),
            $plantModel,
            $repotting->getRepottedAt(),
            $repotting->getDetails()->getType()->value,
            $repotting->getDetails()->getMaterial()->value,
            $repotting->getDetails()->getPotSize(),
            $repotting->getComment(),
            $repotting->getCreatedAt(),
            $repotting->getUpdatedAt()
        );
    }

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => '#',
            'group_id' => 'Идентификатор группы',
            'group_title' => 'Группа',
            'plant_id' => 'Идентификатор растения',
            'plant_title' => 'Название растения',
            'repotted_at' => 'Дата пересадки',
            'type' => 'Тип пересадки',
            'pot_material' => 'Материал горшка',
            'pot_size' => 'Размер горшка',
            'comment' => 'Комментарий',
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
            'plant_id' => $this->getPlantId(),
            'plant_title' => $this->getPlant()?->getTitle(),
            'repotted_at' => $this->getRepottedAt()->setTimezone($timezone)->format('d.m.Y'),
            'type' => RepottingType::tryFrom($this->getType())?->getLabel(),
            'pot_material' => PotMaterial::tryFrom($this->getPotMaterial())?->getLabel(),
            'pot_size' => $this->getPotSize(),
            'comment' => $this->getComment(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s')
        ];
    }
}
