<?php

namespace App\Domain\Entity\Traits;

use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;

trait DeletedAtTrait
{
    #[ORM\Column(name: 'deleted_at', type: 'datetimetz', nullable: true)]
    private ?DateTime $deletedAt = null;

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(): void
    {
        if ($this->deletedAt === null) {
            $this->deletedAt = new DateTime('now', new DateTimeZone('UTC'));
        }
    }
}
