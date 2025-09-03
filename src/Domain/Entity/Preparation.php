<?php //препарат

namespace App\Domain\Entity;

use DateTime;
use Webmozart\Assert\Assert as WebmozartAssert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class Preparation
{
    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: false)]
    private string $title;

    #[ORM\Column(name: 'description', type: 'string', length: 1024, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'manufacturer', type: 'string', length: 255, nullable: false)]
    private string $manufacturer;

    #[ORM\Column(name: 'quantity', type: 'integer', nullable: false)]
    private int $quantity;

    #[ORM\Column(name: 'use_date', type: 'datetime', nullable: false)]
    private DateTime $useDate;

    #[ORM\Column(name: 'comment', type: 'string', length: 1024, nullable: true)]
    private ?string $comment = null;

    private function setCommonFields(
        string $title,
        string $manufacturer,
        int $quantity,
        DateTime $useDate,
        ?string $description = null,
        ?string $comment = null
    ): void {
        $this->setTitleValidate($title);
        $this->setManufacturerValidate($manufacturer);
        $this->setQuantityValidate($quantity);
        $this->setUseDateValidate($useDate);

        $this->description = $description;
        $this->comment = $comment;
    }

    public function __construct(
        string $title,
        string $manufacturer,
        int $quantity,
        DateTime $useDate,
        ?string $description = null,
        ?string $comment = null
    ) {
        $this->setCommonFields($title, $manufacturer, $quantity, $useDate, $description, $comment);
    }

    public function changeFields(
        string $title,
        string $manufacturer,
        int $quantity,
        DateTime $useDate,
        ?string $description = null,
        ?string $comment = null
    ): void
    {
        $this->setCommonFields($title, $manufacturer, $quantity, $useDate, $description, $comment);
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

    private function setManufacturerValidate(string $manufacturer): void
    {
        WebmozartAssert::stringNotEmpty($manufacturer, 'Manufacturer should not be empty. Got: %s');

        // Проверяем, что строка состоит только из русских букв и цифр
        WebmozartAssert::regex(
            $manufacturer,
            '/^[\p{Cyrillic}0-9\s\-_]+$/u',
            'Manufacturer must contain only Cyrillic letters, digits, spaces, hyphens, or underscores. Got: %s'
        );
        WebmozartAssert::lengthBetween($manufacturer, 2, 255, 'Manufacturer must be a string valid length of 2-255 letters. Got: %s');

        $this->manufacturer = $manufacturer;
    }

    private function setQuantityValidate(string $quantity): void
    {
        WebmozartAssert::numeric($quantity);
        WebmozartAssert::greaterThan($quantity, 0, 'Quantity must be positive');
        $this->quantity = $quantity;
    }

    private function setUseDateValidate(DateTime $useDate): void
    {
        WebmozartAssert::isInstanceOf($useDate, DateTime::class, 'Use date must be a DateTime instance');
        $this->useDate = $useDate;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUseDate(): DateTime
    {
        return $this->useDate;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'manufacturer' => $this->manufacturer,
            'quantity' => $this->quantity,
            'use_date' => $this->useDate,
            'comment' => $this->comment,
        ];
    }
}
