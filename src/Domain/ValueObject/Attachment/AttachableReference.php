<?php //для ручной реализации полиморфизма

namespace App\Domain\ValueObject\Attachment;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class AttachableReference
{
    //идентификатор связанной сущности
    #[ORM\Column(name: 'attachable_id', type: 'integer', nullable: true)]
    private ?int $attachableId = null;

    //тип связанной сущности
    #[ORM\Column(name: 'attachable_type', type: 'string', nullable: true)]
    private ?string $attachableType = null;// Тип сущности (Plant, User и т.п.)

    public function __construct(
        ?int $attachableId = null,
        ?string $attachableType = null
    ) {
        $this->attachableId = $attachableId;
        $this->attachableType = $attachableType;
    }

    public function getAttachableId(): ?int
    {
        return $this->attachableId;
    }

    public function getAttachableType(): ?string
    {
        return $this->attachableType;
    }

    // Проверка: к чему привязано?
    public function isPlant(): bool
    {
        return $this->attachableType === 'plant';
    }

    public function isOffspring(): bool
    {
        return $this->attachableType === 'offspring';
    }
}
