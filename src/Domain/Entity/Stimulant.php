<?php //стимуляторы

namespace App\Domain\Entity;

use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'stimulant')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'stimulant__plant_id__ind', columns: ['plant_id'])]
class Stimulant extends Preparation implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    //связь с растениями
    #[ORM\ManyToOne(targetEntity: Plant::class, inversedBy: 'stimulants')]
    #[ORM\JoinColumn(name: 'plant_id', referencedColumnName: 'id')]
    private Plant $plant;

    public function __construct(
        Plant $plant,
        string $title,
        int $quantity,
        string $letter,
        ?string $manufacturer = null,
        ?string $description = null,
        ?string $comment = null
    )
    {
        parent::__construct($title, $quantity, $letter, $manufacturer, $description, $comment);

        $this->plant = $plant;
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    public function getPlant(): Plant
    {
        return $this->plant;
    }

    public function changeFieldsWithPlant(
        string   $title,
        int      $quantity,
        string   $letter,
        Plant    $plant,
        ?string  $manufacturer = null,
        ?string  $description = null,
        ?string  $comment = null,
    ): void
    {
        parent::changeFields($title, $quantity, $letter, $manufacturer, $description, $comment);
        $this->plant = $plant;
    }
}
