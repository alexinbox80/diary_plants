<?php

namespace App\Domain\Entity\Traits;

use DateTime;

trait DeletedAtTrait
{
    private ?DateTime $deletedAt = null;

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(): void
    {
        if ($this->deletedAt === null) {
            $this->deletedAt = new DateTime();
        }
    }
}
