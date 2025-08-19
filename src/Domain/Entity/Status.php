<?php //статус

namespace App\Domain\Entity;

use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;

class Status implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    private ?int $id = null;

    private string $letter;

    private ?string $description = null;

    private string $color;

    private ?string $colorDescription = null;

    public function __construct(
        string $letter,
        string $color,
        ?string $description = null,
        ?string $colorDescription = null
    )
    {
        WebmozartAssert::stringNotEmpty($letter);
        $this->letter = $letter;

        WebmozartAssert::stringNotEmpty($color);
        $this->color = $color;

        $this->description = $description;
        $this->colorDescription = $colorDescription;
    }

    public function changeFields(
        string $letter,
        string $color,
        ?string $description = null,
        ?string $colorDescription = null
    ): void
    {
        $this->letter = $letter;
        $this->color = $color;
        $this->description = $description;
        $this->colorDescription = $colorDescription;
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    public function getLetter(): string
    {
        return $this->letter;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getColorDescription(): ?string
    {
        return $this->colorDescription;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'letter' => $this->getLetter(),
            'color' => $this->getColor(),
            'description' => $this->getDescription(),
            'color_description' => $this->getColorDescription(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('Y-m-d H:i:s')
        ];
    }
}
