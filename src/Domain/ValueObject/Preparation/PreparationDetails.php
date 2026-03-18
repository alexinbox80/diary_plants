<?php //группирует информационные поля.

namespace App\Domain\ValueObject\Preparation;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert as WebmozartAssert;

#[ORM\Embeddable]
class PreparationDetails
{
    private const REGEX_ALPHA_NUM = '/^[ \p{Cyrillic}A-Za-z0-9\s\-_\(\)№]+$/u';

    //описание
    #[ORM\Column(name: 'description', type: 'string', length: 1024, nullable: true)]
    private ?string $description = null;

    //изготовитель
    #[ORM\Column(name: 'manufacturer', type: 'string', length: 255, nullable: true)]
    private ?string $manufacturer = null;


    //комментарии
    #[ORM\Column(name: 'comment', type: 'string', length: 1024, nullable: true)]
    private ?string $comment = null;

    public function __construct(
        ?string $manufacturer = null,
        ?string $description = null,
        ?string $comment = null
    ) {
        if (!is_null($manufacturer)) {
            WebmozartAssert::regex(
                $manufacturer,
                self::REGEX_ALPHA_NUM,
                'Manufacturer contains invalid characters'
            );
        }

        $this->manufacturer = $manufacturer;
        $this->description = $description;
        $this->comment = $comment;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
