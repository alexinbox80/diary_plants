<?php

namespace App\Domain\ValueObject\Plant;

use App\Domain\ValueObject\OId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class PlantIdentifier
{
    //UUIDv4
    #[ORM\Column(type: 'oid', nullable: true)]
    private OId $oid;

    //ссылка на файл с qr кодом
    #[ORM\Column(name: 'qr_code_link', type: 'string', length: 255, nullable: true)]
    private ?string $qrCodeLink = null;

    public function __construct(
        OId $oid,
        ?string $qrCodeLink = null,
    ) {
        $this->oid = $oid;
        $this->qrCodeLink = $qrCodeLink;
    }

    public function withQrCodeLink(string $link): self
    {
        return new self(
            oid: $this->oid,
            qrCodeLink: $link
        );
    }

    public function getOid(): Oid
    {
        return $this->oid;
    }

    public function getQrCodeLink(): ?string
    {
        return $this->qrCodeLink;
    }
}
