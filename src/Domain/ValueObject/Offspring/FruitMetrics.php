<?php //физические данные плода

namespace App\Domain\ValueObject\Offspring;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class FruitMetrics
{
    //масса гр
    #[ORM\Column(name: 'mass', type: 'integer', nullable: true)]
    private ?int $mass = null;

    //цвет
    #[ORM\Column(name: 'color', type: 'string', length: 64, nullable: true)]
    private ?string $color = null;

    //вкус
    #[ORM\Column(name: 'flavor', type: 'string', length: 64, nullable: true)]
    private ?string $flavor = null;

    //количество
    #[ORM\Column(name: 'quantity', type: 'integer', nullable: true)]
    private ?int $quantity = null;

    public function __construct(
        ?int $mass = null,
        ?int $quantity = null,
        ?string $color = null,
        ?string $flavor = null
    ) {
        $this->mass = $mass;
        $this->quantity = $quantity;
        $this->color = $color;
        $this->flavor = $flavor;
    }

    public function getMass(): ?int
    {
        return $this->mass;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getFlavor(): ?string
    {
        return $this->flavor;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }
}
