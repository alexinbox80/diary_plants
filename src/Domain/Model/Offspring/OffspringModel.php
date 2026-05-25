<?php

namespace App\Domain\Model\Offspring;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Offspring;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\ValueObject\Enum\Timezone;
use App\Domain\Model\Interfaces\AttachableModelInterface;

class OffspringModel implements AttachableModelInterface
{
    public function __construct(
        private readonly int $id,
        private readonly int $groupId,
        private readonly int $plantId,
        private readonly array $attachment = [],
        private readonly ?DateTimeImmutable $fruitingDate = null,
        private readonly ?DateTimeImmutable $floweringDate = null,
        private readonly ?int $mass = null,
        private readonly ?string $color = null,
        private readonly ?string $flavor = null,
        private readonly ?int $quantity = null,
        private readonly ?string $comment = null,
        private readonly ?PLantModel $plant = null,
        private readonly ?GroupModel $group = null,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPlantId(): int
    {
        return $this->plantId;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getAttachment(): array
    {
        return $this->attachment;
    }

    public function getFruitingDate(): ?DateTimeImmutable
    {
        return $this->fruitingDate;
    }

    public function getFloweringDate(): ?DateTimeImmutable
    {
        return $this->floweringDate;
    }

    public function getMass(): ?int
    {
        return $this->mass;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getFlavor(): ?string
    {
        return $this->flavor;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getPlant(): ?PLantModel
    {
        return $this->plant;
    }

    public function getGroup(): ?GroupModel
    {
        return $this->group;
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
     * @param Offspring $offspring
     * @param array $attachmentModels
     * @param PlantModel|null $plantModel
     * @param GroupModel|null $groupModel
     * @return OffspringModel
     */
    public static function fromEntity(Offspring $offspring, array $attachmentModels = [], ?PlantModel $plantModel = null, ?GroupModel $groupModel = null): self
    {
        return new self(
            $offspring->getId(),
            $offspring->getGroup()->getId(),
            $offspring->getPlant()->getId(),
            $attachmentModels,
            $offspring->getPhenology()->getFruitingDate(),
            $offspring->getPhenology()->getFloweringDate(),
            $offspring->getFruitMetrics()->getMass(),
            $offspring->getFruitMetrics()->getColor(),
            $offspring->getFruitMetrics()->getFlavor(),
            $offspring->getFruitMetrics()->getQuantity(),
            $offspring->getComment(),
            $plantModel,
            $groupModel,
            $offspring->getCreatedAt(),
            $offspring->getUpdatedAt()
        );
    }

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => 'table.offspring.header.id',
            'group_id' => 'table.offspring.header.group_id',
            'group_title' => 'table.offspring.header.group_title',
            'plant_id' => 'table.offspring.header.plant_id',
            'plant_title' => 'table.offspring.header.plant_title',
            'img_gallery' => 'table.offspring.header.img_gallery',
            'fruiting_date' => 'table.offspring.header.fruiting_date',
            'flowering_date' => 'table.offspring.header.flowering_date',
            'mass' => 'table.offspring.header.mass',
            'word_color' => 'table.offspring.header.color',
            'flavor' => 'table.offspring.header.flavor',
            'quantity' => 'table.offspring.header.quantity',
            'comment' => 'table.offspring.header.comment',
            'created_at' => 'table.offspring.header.created_at',
            'updated_at' => 'table.offspring.header.updated_at'
        ];
    }

    public function toArray(?Timezone $tz = null): array
    {
        if (is_null($tz)) {
            $timezone = new DateTimeZone('Europe/Moscow');
        } else {
            $timezone = new DateTimeZone($tz->value);
        }

        $filtered = array_filter($this->getAttachment(), fn ($attachment) => $attachment->getMimeType() !== null);
        $imgGallery = array_map(fn ($attachment) => $attachment->toArray(), $filtered);

        return [
            'id' => $this->getId(),
            'group_id' => $this->getGroupId(),
            'group_title' => $this->getGroup()?->getTitle(),
            'plant_id' => $this->getPlantId(),
            'plant_title' => $this->getPlant()?->getTitle(),
            'img_gallery' => $imgGallery,
            'fruiting_date' => $this->getFruitingDate()?->format('d.m.Y'),
            'flowering_date' => $this->getFloweringDate()?->format('d.m.Y'),
            'mass' => $this->getMass(),
            'word_color' => $this->getColor(),
            'flavor' => $this->getFlavor(),
            'quantity' => $this->getQuantity(),
            'comment' => $this->getComment(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
        ];
    }
}
