<?php //сокращение от вредителей, стимуляторов, удобрений, полива, задание цвета, визуальная метка в календарях

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use Doctrine\Common\Collections\ArrayCollection;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'marker')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'marker__group_id__ind', columns: ['group_id'])]
#[UniqueEntity(
    fields: ['letter', 'group'],
    message: 'This letter is already used in this group.'
)]
#[ORM\UniqueConstraint(
    name: 'marker__letter_group__uniq',
    columns: ['letter', 'group_id'],
    options: ['where' => '(deleted_at IS NULL)']
)]
class Marker implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    //сокращение
    #[ORM\Column(type: 'string', length: 3, nullable: false)]
    private string $letter;

    //описание
    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    private ?string $description = null;

    //цвет
    #[ORM\Column(type: 'string', length: 7, nullable: false)]
    private string $color;

    //описание цвета
    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    private ?string $colorDescription = null;

    //идентификатор связанной сущности group
    #[ORM\ManyToOne(targetEntity: Group::class, cascade: ['all'], fetch: 'EAGER', inversedBy: 'markers')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

    //связь с поливом
    #[ORM\OneToMany(targetEntity: Watering::class, mappedBy: 'group', cascade: ['remove'])]
    private Collection $waterings;

    private function setCommonFields(
        Group $group,
        string $letter,
        string $color,
        ?string $description = null,
        ?string $colorDescription = null
    ): void {
        $this->setGroupValidate($group);
        $this->setLetterValidate($letter);
        $this->setColorValidate($color);
        $this->setDescriptionValidate($description);
        $this->setColorDescriptionValidate($colorDescription);
    }

    private function setGroupValidate(Group $group): void
    {
        $this->group = $group;
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
        Group $group,
        string $letter,
        string $color,
        ?string $description = null,
        ?string $colorDescription = null
    )
    {
        $this->setCommonFields($group, $letter, $color, $description, $colorDescription);

        $this->waterings = new ArrayCollection();
    }

    public function changeFields(
        Group $group,
        string $letter,
        string $color,
        ?string $description = null,
        ?string $colorDescription = null
    ): void
    {
        $this->setCommonFields($group, $letter, $color, $description, $colorDescription);
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    public function getGroup(): Group
    {
        return $this->group;
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
}
