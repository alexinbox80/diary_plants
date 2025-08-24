<?php //препарат

namespace App\Domain\Entity;

use DateTime;
use Webmozart\Assert\Assert as WebmozartAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
class Preparation
{
    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: false)]
    private string $title;

    #[ORM\Column(name: 'description', type: 'string', length: 255, nullable: false)]
    private ?string $description;

    #[ORM\Column(name: 'manufacturer', type: 'string', length: 255, nullable: false)]
    private string $manufacturer;

    #[ORM\Column(name: 'quantity', type: 'integer', nullable: false)]
    private int $quantity;

    #[ORM\Column(name: 'use_date', type: 'datetime', nullable: false)]
    private DateTime $useDate;

    public function __construct(
        string $title,
        string $manufacturer,
        int $quantity,
        DateTime $useDate,
        ?string $description = null,
    ) {
        WebmozartAssert::stringNotEmpty($title);
        $this->title = $title;

        WebmozartAssert::stringNotEmpty($manufacturer);
        $this->manufacturer = $manufacturer;

        WebmozartAssert::Numeric($quantity);
        $this->quantity = $quantity;

        $this->useDate = $useDate;

        $this->description = $description;
    }

    public function changeFields(
        string $title,
        string $manufacturer,
        int $quantity,
        DateTime $useDate,
        ?string $description = null,
    ): void
    {
        $this->title = $title;
        $this->manufacturer = $manufacturer;
        $this->quantity = $quantity;
        $this->useDate = $useDate;
        $this->description = $description;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUseDate(): DateTime
    {
        return $this->useDate;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'manufacturer' => $this->manufacturer,
            'quantity' => $this->quantity,
            'use_date' => $this->useDate,
        ];
    }
}
