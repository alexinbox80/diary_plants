<?php

namespace App\Domain\Model\Watering;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Watering;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Marker\MarkerModel;
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
        private readonly DateTimeImmutable $wateredAt,
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

    public function getWateredAt(): DateTimeImmutable
    {
        return $this->wateredAt;
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
    static function fromEntity(Watering $watering, ?GroupModel $groupModel = null, ?MarkerModel $markerModel = null): self
    {
        return new self(
            $watering->getId(),
            $watering->getGroup()->getId(),
            $watering->getMarker()->getId(),
            $watering->getDetails()->getAmount(),
            $watering->getDetails()->getType()->value,
            $watering->getDetails()->getMethod()->value,
            $watering->getWateredAt(),
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
            'id' => '#',
            'icon_tag' => 'Маркер',
            'group_id' => 'Идентификатор группы',
            'marker_id' => 'Идентификатор сокращения',
            'group_title' => 'Группа',
            'marker_letter' => 'Обозначение',
            'marker_color' => 'Цвет',
            'amount' => 'Количество',
            'water_type' => 'Тип полива',
            'watering_method' => 'Метод полива',
            'watered_at' => 'Дата полива',
            'temperature' => 'Температура',
            'description' => 'Описание',
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
            'icon_tag' => '',
            'group_id' => $this->getGroupId(),
            'group_title' => $this->getGroup()?->getTitle(),
            'marker_id' => $this->getMarkerId(),
            'marker_letter' => $this->getMarker()?->getLetter(),
            'marker_color' => $this->getMarker()?->getColor(),
            'amount' => $this->getAmount(),
            'water_type' => WaterType::tryFrom($this->getWaterType())?->getLabel(),
            'watering_method' => WateringMethod::tryFrom($this->getWateringMethod())?->getLabel(),
            'watered_at' => $this->getWateredAt()?->setTimezone($timezone)->format('d.m.Y'),
            'temperature' => $this->getTemperature(),
            'description' => $this->getDescription(),
            'comment' => $this->getComment(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s')
        ];
    }
}
