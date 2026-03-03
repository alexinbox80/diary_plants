<?php

namespace App\Domain\Entity\Traits;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\User;
use App\Domain\Entity\Plant;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\Entity\Attachment;
use App\Domain\ValueObject\Attachment\FileInfo;
use App\Domain\ValueObject\Plant\PlantIdentifier;

trait DeletedAtTrait
{
    #[ORM\Column(name: 'deleted_at', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $deletedAt = null;

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(): void
    {
        if ($this instanceof Attachment) {
            $this->updateFileInfo(
                new FileInfo()
            );
        }
        if ($this instanceof Plant) {
            $this->plantIdentifier = new PlantIdentifier(
                $this->getPlantIdentifier()->getOid()
            );
        }
        if ($this instanceof User) {
            $this->avatarLink = null;
        }
        if ($this->deletedAt === null) {
            $this->deletedAt = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        }
    }
}
