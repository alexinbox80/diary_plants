<?php

namespace App\Domain\Entity\Traits;

use DateTime;

trait UpdatedAtTrait
{
    private DateTime $updatedAt;

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): void
    {
        $this->updatedAt = new DateTime();
    }
}
