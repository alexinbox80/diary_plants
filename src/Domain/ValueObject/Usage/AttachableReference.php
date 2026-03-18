<?php //для ручной реализации полиморфизма

namespace App\Domain\ValueObject\Usage;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\ValueObject\Enum\Usage\AttachableType;

#[ORM\Embeddable]
class AttachableReference
{
    #[ORM\Column(name: 'usable_id', type: 'bigint', nullable: true)]
    private ?int $usableId = null;

    #[ORM\Column(name: 'usable_type', type: 'string', nullable: true, enumType: AttachableType::class)]
    private ?AttachableType $usableType = null;

    public function __construct(
        ?int $usableId = null,
        ?AttachableType $usableType = null
    ) {
        $this->usableId = $usableId;
        $this->usableType = $usableType;
    }

    public function getUsableId(): ?int
    {
        return $this->usableId;
    }

    public function getUsableType(): ?AttachableType
    {
        return $this->usableType;
    }

    // Проверка: к чему привязано?
    public function isPest(): bool
    {
        return $this->usableType === AttachableType::PEST;
    }

    public function isStimulant(): bool
    {
        return $this->usableType === AttachableType::STIMULANT;
    }

    public function isFertilizer(): bool
    {
        return $this->usableType === AttachableType::FERTILIZER;
    }

    public function isWatering(): bool
    {
        return $this->usableType === AttachableType::WATERING;
    }
}
