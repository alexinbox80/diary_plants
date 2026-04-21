<?php

namespace App\Domain\Model\Analytic;

use DateTimeImmutable;
use App\Domain\Entity\Analytic;

class AnalyticModel
{
    public function __construct(
        private readonly int $id,
        private readonly int $plantId,
        private readonly int $groupId,
        private readonly int $count = 0,
        private readonly float $averageDays = 0.0,
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

    public function getCount(): int
    {
        return $this->count;
    }

    public function getAverageDays(): float
    {
        return $this->averageDays;
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
     * @param Analytic $analytic
     * @return AnalyticModel
     */
    public static function fromEntity(Analytic $analytic): self
    {
        return new self(
            $analytic->getId(),
            $analytic->getPlant()->getId(),
            $analytic->getGroup()->getId(),
            $analytic->getWateringMetrics()->getCount(),
            $analytic->getWateringMetrics()->getAverageDays(),
            $analytic->getCreatedAt(),
            $analytic->getUpdatedAt()
        );
    }
}
