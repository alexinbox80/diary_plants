<?php //статус

namespace App\Domain\Entity;

use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Webmozart\Assert\Assert as WebmozartAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'status')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: 'letter', message: 'This letter is already in use.')]
class Status implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 1, unique: true, nullable: false)]
    private string $letter;

    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 7, nullable: false)]
    private string $color;

    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    private ?string $colorDescription = null;

    #[ORM\OneToOne(targetEntity: Task::class, mappedBy: 'status')]
    private Task $task;

    private function setCommonFields(
        string $letter,
        string $color,
        ?string $description = null,
        ?string $colorDescription = null
    ): void {
        $this->setLetterValidate($letter);
        $this->setColorValidate($color);
        $this->setDescriptionValidate($description);
        $this->setColorDescriptionValidate($colorDescription);
    }

    private function setLetterValidate(string $letter): void
    {
        WebmozartAssert::stringNotEmpty($letter, 'Letter should not be empty. Got: %s');
        WebmozartAssert::lengthBetween($letter, 1, 2, 'Title must be a string valid length of 1-2 letters. Got: %s');

        $this->letter = $letter;
    }

    private function setColorValidate(string $color): void
    {
        WebmozartAssert::stringNotEmpty($color, 'Color should not be empty. Got: %s');
        WebmozartAssert::lengthBetween($color, 7, 7, 'Color must be a string valid length of 7 letters. Got: %s');

        WebmozartAssert::regex(
            $color,
            '/^#([A-Fa-f0-9]{6})$/',
            'Invalid color format. Expected #RRGGBB. Got: %s'
        );

        $this->color = $color;
    }

    private function setDescriptionValidate(?string $description = null): void
    {
        WebmozartAssert::lengthBetween($description, 3, 1024, 'Description must be a string valid length of of 3-1024 letters. Got: %s');

        $this->description = $description;
    }

    private function setColorDescriptionValidate(?string $colorDescription = null): void
    {
        WebmozartAssert::lengthBetween($colorDescription, 3, 1024, 'Description must be a string valid length of of 3-1024 letters. Got: %s');

        $this->colorDescription = $colorDescription;
    }

    public function __construct(
        string $letter,
        string $color,
        ?string $description = null,
        ?string $colorDescription = null
    )
    {
        $this->setCommonFields($letter, $color, $description, $colorDescription);
    }

    public function changeFields(
        string $letter,
        string $color,
        ?string $description = null,
        ?string $colorDescription = null
    ): void
    {
        $this->setCommonFields($letter, $color, $description, $colorDescription);
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
