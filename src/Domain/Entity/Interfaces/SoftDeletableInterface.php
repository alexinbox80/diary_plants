<?php

namespace App\Domain\Entity\Interfaces;

use DateTime;

interface SoftDeletableInterface
{
    public function getDeletedAt(): ?DateTime;

    public function setDeletedAt(): void;
}
