<?php

namespace App\Domain\Model\Watering;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Marker\MarkerModel;

class WateringModel
{
    public function __construct(
        private readonly int $id,
        private readonly int $groupId,
        private readonly int $markerId,
        private readonly int $amount,
        private readonly string $wateringType,
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

    public function getWateringType(): string
    {
        return $this->wateringType;
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

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => '#',
            'group_id' => 'Идентификатор группы',
            'marker_id' => 'Идентификатор сокращения',
            'group_title' => 'Группа',
            'marker_letter' => 'Обозначение',
            'amount' => 'Количество',
            'watering_type' => 'Тип полива',
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
            'group_id' => $this->getGroupId(),
            'group_title' => $this->getGroup()?->getTitle(),
            'marker_id' => $this->getMarkerId(),
            'marker_letter' => $this->getMarker()?->getLetter(),
            'amount' => $this->getAmount(),
            'watering_type' => $this->getWateringType(),
            'watering_method' => $this->getWateringMethod(),
            'watered_at' => $this->getWateredAt(),
            'temperature' => $this->getTemperature(),
            'description' => $this->getDescription(),
            'comment' => $this->getComment(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s')
        ];
    }
}
