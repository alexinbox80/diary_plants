<?php //жизненный цикл растения

namespace App\Domain\ValueObject\Plant;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class LifeCycle
{
    //дата прививки
    #[ORM\Column(name: 'vaccination_date', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $vaccinationDate = null;

    //дата посадки
    #[ORM\Column(name: 'planting_date', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $plantingDate = null;

    //описание грунта
    #[ORM\Column(name: 'soil', type: 'string', length: 1024, nullable: true)]
    private ?string $soil = null;

    public function __construct(
        ?DateTimeImmutable $plantingDate = null,
        ?DateTimeImmutable $vaccinationDate = null,
        ?string $soil = null
    ) {
        $this->plantingDate = $plantingDate;
        $this->vaccinationDate = $vaccinationDate;
        $this->soil = $soil;
    }

    public function getVaccinationDate(): ?DateTimeImmutable
    {
        return $this->vaccinationDate;
    }

    public function getPlantingDate(): ?DateTimeImmutable
    {
        return $this->plantingDate;
    }

    public function getSoil(): ?string
    {
        return $this->soil;
    }
}
