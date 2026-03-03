<?php //Отвечает за количество и буквенное обозначение

namespace App\Domain\ValueObject\Preparation;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert as WebmozartAssert;

#[ORM\Embeddable]
class PreparationVolume
{
    //количество
    #[ORM\Column(name: 'quantity', type: 'integer', nullable: false)]
    private int $quantity;

    //буква обозначения
    #[ORM\Column(name: 'letter', type: 'string', length: 2, nullable: false)]
    private string $letter;

    public function __construct(
        int $quantity,
        string $letter
    ) {
        WebmozartAssert::greaterThan($quantity, 0, 'Quantity must be positive');
        $this->quantity = $quantity;

        WebmozartAssert::lengthBetween($letter, 2, 2, 'Letter must be exactly 2 characters');
        $this->letter = $letter;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getLetter(): string
    {
        return $this->letter;
    }
}
