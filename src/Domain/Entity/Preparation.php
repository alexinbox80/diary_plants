<?php //общий наследуемый класс для стимуляторов удобрений и вредителей

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert as WebmozartAssert;
use App\Domain\ValueObject\Preparation\PreparationVolume;
use App\Domain\ValueObject\Preparation\PreparationDetails;

#[ORM\MappedSuperclass]
abstract class Preparation
{
    private const REGEX_ALPHA_NUM = '/^[ \p{Cyrillic}A-Za-z0-9\s\-_\(\)№]+$/u';

    //заголовок
    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: false)]
    private string $title;

    //количество и норма расхода
    #[ORM\Embedded(class: PreparationVolume::class, columnPrefix: false)]
    private PreparationVolume $volume;

    //описание, изготовитель, комментарии
    #[ORM\Embedded(class: PreparationDetails::class, columnPrefix: false)]
    private PreparationDetails $details;

    public function __construct(
        string $title,
        PreparationVolume $volume,
        PreparationDetails $details = new PreparationDetails()
    ) {
        $this->setTitleValidate($title);
        $this->volume = $volume;
        $this->details = $details;
    }

    protected function changeFields(
        string $title,
        PreparationVolume $volume,
        PreparationDetails $details
    ): void
    {
        $this->setTitleValidate($title);
        $this->volume = $volume;
        $this->details = $details;
    }

    private function setTitleValidate(string $title): void
    {
        WebmozartAssert::stringNotEmpty($title, 'Title should not be empty. Got: %s');

        // Проверяем, что строка состоит только из русских букв и цифр
        WebmozartAssert::regex(
            $title,
            self::REGEX_ALPHA_NUM,
            'Title must contain only Cyrillic and Latin letters, digits, spaces, hyphens, or underscores. Got: %s'
        );
        WebmozartAssert::lengthBetween($title, 2, 255, 'Title must be a string valid length of 2-255 letters. Got: %s');

        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getVolume(): PreparationVolume
    {
        return $this->volume;
    }

    public function getDetails(): PreparationDetails
    {
        return $this->details;
    }
}
