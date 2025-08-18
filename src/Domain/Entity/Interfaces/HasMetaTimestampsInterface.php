<?php

namespace App\Domain\Entity\Interfaces;

interface HasMetaTimestampsInterface
{
    public function setCreatedAt(): void;

    public function setUpdatedAt(): void;
}
