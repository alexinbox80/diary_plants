<?php //для ручной реализации полиморфизма

namespace App\Domain\ValueObject\Attachment;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\ValueObject\Enum\Attachment\AttachableType;

#[ORM\Embeddable]
class AttachableReference
{
    //идентификатор связанной сущности
    #[ORM\Column(name: 'attachable_id', type: 'integer', nullable: true)]
    private ?int $attachableId = null;

    //тип связанной сущности
    #[ORM\Column(name: 'attachable_type', type: 'string', nullable: true, enumType: AttachableType::class)]
    private ?AttachableType $attachableType = null;// Тип сущности (Plant, User и т.п.)

    public function __construct(
        ?int $attachableId = null,
        ?AttachableType $attachableType = null
    ) {
        $this->attachableId = $attachableId;
        $this->attachableType = $attachableType;
    }

    public function getAttachableId(): ?int
    {
        return $this->attachableId;
    }

    public function getAttachableType(): ?AttachableType
    {
        return $this->attachableType;
    }

    // Проверка: к чему привязано?
    public function isPlant(): bool
    {
        return $this->attachableType === AttachableType::from('plant');
    }

    public function isOffspring(): bool
    {
        return $this->attachableType === AttachableType::from('offspring');
    }
}
