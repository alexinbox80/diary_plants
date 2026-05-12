<?php

namespace App\Domain\Model\Pest;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Pest;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Marker\MarkerModel;
use App\Domain\ValueObject\Enum\Timezone;
use App\Domain\Model\Interfaces\AttachableModelInterface;

class PestModel implements AttachableModelInterface
{
    public function __construct(
        private readonly int $id,
        private readonly int $groupId,
        private readonly int $markerId,
        private readonly string $title,
        private readonly int $amount,
        private readonly ?string $manufacturer = null,
        private readonly ?string $applicationRate = null,
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getApplicationRate(): ?string
    {
        return $this->applicationRate;
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
     * @param Pest $pest
     * @param GroupModel|null $groupModel
     * @param MarkerModel|null $markerModel
     * @return PestModel
     */
    public static function fromEntity(Pest $pest, ?GroupModel $groupModel = null, ?MarkerModel $markerModel = null): self
    {
        return new self(
            $pest->getId(),
            $pest->getGroup()->getId(),
            $pest->getMarker()->getId(),
            $pest->getTitle(),
            $pest->getVolume()->getAmount(),
            $pest->getDetails()->getManufacturer(),
            $pest->getVolume()->getApplicationRate(),
            $pest->getDetails()->getDescription(),
            $pest->getDetails()->getComment(),
            $groupModel,
            $markerModel,
            $pest->getCreatedAt(),
            $pest->getUpdatedAt()
        );
    }

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => 'table.pest.header.id',
            'icon_tag' => 'table.pest.header.icon_tag',
            'group_id' => 'table.pest.header.group_id',
            'group_title' => 'table.pest.header.group_title',
            'marker_id' => 'table.pest.header.marker_id',
            'marker_letter' => 'table.pest.header.marker_letter',
            'marker_color' => 'table.pest.header.marker_color',
            'title' => 'table.pest.header.title',
            'amount' => 'table.pest.header.amount',
            'application_rate' => 'table.pest.header.application_rate',
            'manufacturer' => 'table.pest.header.manufacturer',
            'description' => 'table.pest.header.description',
            'comment' => 'table.pest.header.comment',
            'created_at' => 'table.pest.header.created_at',
            'updated_at' => 'table.pest.header.updated_at'
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
            'title' => $this->getTitle(),
            'amount' => $this->getAmount(),
            'application_rate' => $this->getApplicationRate(),
            'manufacturer' => $this->getManufacturer(),
            'description' => $this->getDescription(),
            'comment' => $this->getComment(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s')
        ];
    }
}
