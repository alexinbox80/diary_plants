<?php //группирует поля пересадки, материала и размера

namespace App\Domain\ValueObject\Repotting;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\ValueObject\Enum\Repotting\PotMaterial;
use App\Domain\ValueObject\Enum\Repotting\RepottingType;

#[ORM\Embeddable]
class RepottingDetails
{
    public function __construct(
        //тип пересадки перевалка, полная пересадка, деление куста, замена верхнего слоя, пикировка, посадка саженца, обрезка корней, реанимационная пересадка
        #[ORM\Column(type: 'string', length: 50, enumType: RepottingType::class)]
        private RepottingType $type,

        //материал горшка пластик, керамика, терракота (глина), текстильный мешок, торфяной стаканчик
        #[ORM\Column(type: 'string', length: 50, enumType: PotMaterial::class)]
        private PotMaterial $potMaterial,

        //размер горшка диаметры см. 10, 12, 14
        #[ORM\Column(name: 'pot_size', type: 'string', length: 32)]
        private string $potSize
    ) {}

    public function getType(): RepottingType
    {
        return $this->type;
    }

    public function getMaterial(): PotMaterial
    {
        return $this->potMaterial;
    }

    public function getPotSize(): string
    {
        return $this->potSize;
    }
}
