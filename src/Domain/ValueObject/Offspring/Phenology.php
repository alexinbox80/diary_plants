<?php //даты жизненного цикла

namespace App\Domain\ValueObject\Offspring;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Phenology
{
    //дата сбора
    #[ORM\Column(name: 'fruiting_date', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $fruitingDate = null;

    //дата цветения
    #[ORM\Column(name: 'flowering_date', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $floweringDate = null;

    public function __construct(
        ?DateTimeImmutable $fruitingDate = null,
        ?DateTimeImmutable $floweringDate = null
    ) {
        $this->fruitingDate = $fruitingDate;
        $this->floweringDate = $floweringDate;
    }

    public function getFruitingDate(): ?DateTimeImmutable
    {
        return $this->fruitingDate;
    }

    public function getFloweringDate(): ?DateTimeImmutable
    {
        return $this->floweringDate;
    }
}
