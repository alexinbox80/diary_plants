<?php //для ручной реализации полиморфизма

namespace App\Domain\ValueObject\Usage;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class AttachableReference
{
    #[ORM\Column(name: 'usable_id', type: 'bigint', nullable: true)]
    private ?int $usableId = null;

    #[ORM\Column(name: 'usable_type', type: 'string', nullable: true)]
    private ?string $usableType = null;

    public function __construct(
        ?int $usableId = null,
        ?string $usableType = null
    ) {
        $this->usableId = $usableId;
        $this->usableType = $usableType;
    }

    public function getUsableId(): ?int
    {
        return $this->usableId;
    }

    public function getUsableType(): ?string
    {
        return $this->usableType;
    }

    // Проверка: к чему привязано?
    public function isPest(): bool
    {
        return $this->usableType === 'pest';
    }

    public function isStimulant(): bool
    {
        return $this->usableType === 'stimulant';
    }

    public function isFertilizer(): bool
    {
        return $this->usableType === 'fertilizer';
    }
}
