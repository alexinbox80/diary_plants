<?php

namespace App\Domain\Entity\Traits;

use DateTime;

trait CreatedAtTrait
{
    private DateTime $createdAt;

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): void
    {
        $this->createdAt = new DateTime();
    }
}
