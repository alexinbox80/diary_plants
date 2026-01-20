<?php

namespace App\Domain\Entity\Traits;

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;

trait DeletedAtTrait
{
    #[ORM\Column(name: 'deleted_at', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(): void
    {
        if ($this->deletedAt === null) {
            $this->deletedAt = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        }
    }
}
