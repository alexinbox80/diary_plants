<?php

namespace App\Domain\Model\Watering;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Watering;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Marker\MarkerModel;
use App\Domain\ValueObject\Enum\Timezone;
use App\Domain\ValueObject\Enum\Watering\WaterType;
use App\Domain\ValueObject\Enum\Watering\WateringMethod;
use App\Domain\Model\Interfaces\AttachableModelInterface;

class WateringModel implements AttachableModelInterface
{
    public function __construct(
        private readonly int $id,
        private readonly int $groupId,
        private readonly int $markerId,
        private readonly int $amount,
        private readonly string $waterType,
        private readonly string $wateringMethod,
        private readonly ?string $temperature = null,
        private readonly ?string $description = null,
        private readonly ?string $comment = null,
        private readonly ?GroupModel $group = null,
        private readonly ?MarkerModel $marker = null,
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

    public function getMarkerId(): int
    {
        return $this->markerId;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getWaterType(): string
    {
        return $this->waterType;
    }

    public function getWateringMethod(): string
    {
        return $this->wateringMethod;
    }

    public function getTemperature(): string
    {
        return $this->temperature;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getGroup(): ?GroupModel
    {
        return $this->group;
    }

    public function getMarker(): ?MarkerModel
    {
        return $this->marker;
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
     * @param Watering $watering
     * @param GroupModel|null $groupModel
     * @param MarkerModel|null $markerModel
     * @return WateringModel
     */
    public static function fromEntity(Watering $watering, ?GroupModel $groupModel = null, ?MarkerModel $markerModel = null): self
    {
        return new self(
            $watering->getId(),
            $watering->getGroup()->getId(),
            $watering->getMarker()->getId(),
            $watering->getDetails()->getAmount(),
            $watering->getDetails()->getType()->value,
            $watering->getDetails()->getMethod()->value,
            $watering->getDetails()->getTemperature(),
            $watering->getDescription(),
            $watering->getComment(),
            $groupModel,
            $markerModel,
            $watering->getCreatedAt(),
            $watering->getUpdatedAt()
        );
    }

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => 'table.watering.header.id',
            'icon_tag' => 'table.watering.header.icon_tag',
            'group_id' => 'table.watering.header.group_id',
            'group_title' => 'table.watering.header.group_title',
            'marker_id' => 'table.watering.header.marker_id',
            'marker_letter' => 'table.watering.header.marker_letter',
            'marker_color' => 'table.watering.header.marker_color',
            'amount' => 'table.watering.header.amount',
            'water_type' => 'table.watering.header.water_type',
            'watering_method' => 'table.watering.header.watering_method',
            'temperature' => 'table.watering.header.temperature',
            'description' => 'table.watering.header.description',
            'comment' => 'table.watering.header.comment',
            'created_at' => 'table.watering.header.created_at',
            'updated_at' => 'table.watering.header.created_at',
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
            'icon_tag' => '',
            'group_id' => $this->getGroupId(),
            'group_title' => $this->getGroup()?->getTitle(),
            'marker_id' => $this->getMarkerId(),
            'marker_letter' => $this->getMarker()?->getLetter(),
            'marker_color' => $this->getMarker()?->getColor(),
            'amount' => $this->getAmount(),
            'water_type' => WaterType::tryFrom($this->getWaterType())?->getLabel(),
            'watering_method' => WateringMethod::tryFrom($this->getWateringMethod())?->getLabel(),
            'temperature' => $this->getTemperature(),
            'description' => $this->getDescription(),
            'comment' => $this->getComment(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s')
        ];
    }
}
