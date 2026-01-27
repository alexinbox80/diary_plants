<?php //общий наследуемый класс для стимуляторов удобрений и вредителей

namespace App\Domain\Entity;

use Webmozart\Assert\Assert as WebmozartAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class Preparation
{
    //заголовок
    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: false)]
    private string $title;

    //буква обозначения
    #[ORM\Column(name: 'letter', type: 'string', length: 2, nullable: false)]
    private string $letter;

    //описание
    #[ORM\Column(name: 'description', type: 'string', length: 1024, nullable: true)]
    private ?string $description = null;

    //изготовитель
    #[ORM\Column(name: 'manufacturer', type: 'string', length: 255, nullable: true)]
    private ?string $manufacturer = null;

    //количество
    #[ORM\Column(name: 'quantity', type: 'integer', nullable: false)]
    private int $quantity;

    //комментарии
    #[ORM\Column(name: 'comment', type: 'string', length: 1024, nullable: true)]
    private ?string $comment = null;

    private function setCommonFields(
        string $title,
        int $quantity,
        string $letter,
        ?string $manufacturer = null,
        ?string $description = null,
        ?string $comment = null
    ): void {
        $this->setTitleValidate($title);
        $this->setManufacturerValidate($manufacturer);
        $this->setQuantityValidate($quantity);
        $this->setLetterValidate($letter);

        $this->description = $description;
        $this->comment = $comment;
    }

    public function __construct(
        string $title,
        int $quantity,
        string $letter,
        ?string $manufacturer = null,
        ?string $description = null,
        ?string $comment = null
    ) {
        $this->setCommonFields($title, $quantity, $letter, $manufacturer, $description, $comment);
    }

    protected function changeFields(
        string $title,
        int $quantity,
        string $letter,
        ?string $manufacturer = null,
        ?string $description = null,
        ?string $comment = null
    ): void
    {
        $this->setCommonFields($title, $quantity, $letter, $manufacturer, $description, $comment);
    }

    private function setTitleValidate(string $title): void
    {
        WebmozartAssert::stringNotEmpty($title, 'Title should not be empty. Got: %s');

        // Проверяем, что строка состоит только из русских букв и цифр
        WebmozartAssert::regex(
            $title,
            '/^[\p{Cyrillic}0-9\s\-_]+$/u',
            'Title must contain only Cyrillic letters, digits, spaces, hyphens, or underscores. Got: %s'
        );
        WebmozartAssert::lengthBetween($title, 2, 255, 'Title must be a string valid length of 2-255 letters. Got: %s');

        $this->title = $title;
    }

    private function setManufacturerValidate(?string $manufacturer = null): void
    {
        //WebmozartAssert::stringNotEmpty($manufacturer, 'Manufacturer should not be empty. Got: %s');

        // Проверяем, что строка состоит только из русских букв и цифр
        if ($manufacturer !== null) {
            WebmozartAssert::regex(
                $manufacturer,
                '/^[\p{Cyrillic}0-9\s\-_()]+$/u',
                'Manufacturer must contain only Cyrillic letters, digits, spaces, hyphens, or underscores. Got: %s'
            );
            WebmozartAssert::lengthBetween($manufacturer, 2, 255, 'Manufacturer must be a string valid length of 2-255 letters. Got: %s');
        }

        $this->manufacturer = $manufacturer;
    }

    private function setQuantityValidate(string $quantity): void
    {
        WebmozartAssert::numeric($quantity);
        WebmozartAssert::greaterThan($quantity, 0, 'Quantity must be positive');
        $this->quantity = $quantity;
    }

    private function setLetterValidate(string $letter): void
    {
        WebmozartAssert::stringNotEmpty($letter, 'Room should not be empty. Got: %s');
        WebmozartAssert::lengthBetween($letter, 2, 2, 'Room must be a string valid length of 2 letters. Got: %s');

        $this->letter = $letter;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getLetter(): string
    {
        return $this->letter;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'manufacturer' => $this->getManufacturer(),
            'quantity' => $this->getQuantity(),
            'letter' => $this->getLetter(),
            'comment' => $this->getComment(),
        ];
    }
}
