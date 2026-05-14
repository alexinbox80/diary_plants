<?php

namespace App\Domain\Model\Fertilizer;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Fertilizer;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Marker\MarkerModel;
use App\Domain\ValueObject\Enum\Timezone;
use App\Domain\Model\Interfaces\AttachableModelInterface;

class FertilizerModel implements AttachableModelInterface
{
    public function __construct(
        private readonly int $id,
        private readonly int $groupId,
        private readonly int $markerId,
        private readonly string $title,
        private readonly int $amount,
        private readonly string $manufacturer,
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

    public function getManufacturer(): string
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
     * @param Fertilizer $fertilizer
     * @param GroupModel|null $groupModel
     * @param MarkerModel|null $markerModel
     * @return FertilizerModel
     */
    public static function fromEntity(Fertilizer $fertilizer, ?GroupModel $groupModel = null, ?MarkerModel $markerModel = null): self
    {
        return new self(
            $fertilizer->getId(),
            $fertilizer->getGroup()->getId(),
            $fertilizer->getMarker()->getId(),
            $fertilizer->getTitle(),
            $fertilizer->getVolume()->getAmount(),
            $fertilizer->getDetails()->getManufacturer(),
            $fertilizer->getVolume()->getApplicationRate(),
            $fertilizer->getDetails()->getDescription(),
            $fertilizer->getDetails()->getComment(),
            $groupModel,
            $markerModel,
            $fertilizer->getCreatedAt(),
            $fertilizer->getUpdatedAt()
        );
    }

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => 'table.fertilizer.header.id',
            'icon_tag' => 'table.fertilizer.header.icon_tag',
            'group_id' => 'table.fertilizer.header.group_id',
            'group_title' => 'table.fertilizer.header.group_title',
            'marker_id' => 'table.fertilizer.header.marker_id',
            'marker_letter' => 'table.fertilizer.header.marker_letter',
            'marker_color' => 'table.fertilizer.header.marker_color',
            'title' => 'table.fertilizer.header.title',
            'amount' => 'table.fertilizer.header.amount',
            'application_rate' => 'table.fertilizer.header.application_rate',
            'manufacturer' => 'table.fertilizer.header.manufacturer',
            'description' => 'table.fertilizer.header.description',
            'comment' => 'table.fertilizer.header.comment',
            'created_at' => 'table.fertilizer.header.created_at',
            'updated_at' => 'table.fertilizer.header.updated_at'
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
