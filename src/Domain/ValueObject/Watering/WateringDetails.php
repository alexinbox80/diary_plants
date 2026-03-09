<?php //детали полива

namespace App\Domain\ValueObject\Watering;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\ValueObject\Enum\Watering\WaterType;
use App\Domain\ValueObject\Enum\Watering\WateringMethod;

#[ORM\Embeddable]
class WateringDetails
{
    //количество воды в мл
    #[ORM\Column(type: 'integer', nullable: false)]
    private int $amount;

    //тип воды
    #[ORM\Column(type: 'string', enumType: WaterType::class)]
    private WaterType $type = WaterType::FILTERED;

    //метод полива
    #[ORM\Column(type: 'string', enumType: WateringMethod::class)]
    private WateringMethod $method = WateringMethod::TOP;

    //температура воды
    #[ORM\Column(type: 'decimal', precision: 4, scale: 1, nullable: true)]
    private ?string $temperature = null;

    public function __construct(
        int $amount,
        WaterType $type = WaterType::FILTERED,
        WateringMethod $method = WateringMethod::TOP,
        ?string $temperature = null,
    ) {
        $this->amount = $amount;
        $this->type = $type;
        $this->method = $method;
        $this->temperature = $temperature;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getType(): WaterType
    {
        return $this->type;
    }

    public function getMethod(): WateringMethod
    {
        return $this->method;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature ? (float)$this->temperature : null;
    }
}
