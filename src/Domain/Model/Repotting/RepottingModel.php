<?php

namespace App\Domain\Model\Repotting;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Repotting;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\ValueObject\Enum\Timezone;
use App\Domain\ValueObject\Enum\Repotting\PotMaterial;
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
            'id' => 'table.repotting.header.id',
            'group_id' => 'table.repotting.header.group_id',
            'group_title' => 'table.repotting.header.group_title',
            'plant_id' => 'table.repotting.header.plant_id',
            'plant_title' => 'table.repotting.header.plant_title',
            'repotted_at' => 'table.repotting.header.repotted_at',
            'type' => 'table.repotting.header.type',
            'pot_material' => 'table.repotting.header.pot_material',
            'pot_size' => 'table.repotting.header.pot_size',
            'comment' => 'table.repotting.header.comment',
            'created_at' => 'table.repotting.header.created_at',
            'updated_at' => 'table.repotting.header.updated_at'
        ];
    }

    public function toArray(?Timezone $tz = null): array
    {
        if (is_null($tz)) {
            $timezone = new DateTimeZone('Europe/Moscow');
        } else {
            $timezone = new DateTimeZone($tz->value);
        }

        return [
            'id' => $this->getId(),
            'group_id' => $this->getGroupId(),
            'group_title' => $this->getGroup()?->getTitle(),
            'plant_id' => $this->getPlantId(),
            'plant_title' => $this->getPlant()?->getTitle(),
            'repotted_at' => $this->getRepottedAt()->setTimezone($timezone)->format('d.m.Y'),
            'type' => RepottingType::tryFrom($this->getType())?->getLabelKey(),
            'pot_material' => PotMaterial::tryFrom($this->getPotMaterial())?->getLabelKey(),
            'pot_size' => $this->getPotSize(),
            'comment' => $this->getComment(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s')
        ];
    }
}
