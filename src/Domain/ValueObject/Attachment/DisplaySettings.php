<?php //содержит UI-логику

namespace App\Domain\ValueObject\Attachment;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class DisplaySettings
{
    //альтернативный заголовок
    #[ORM\Column(name: 'alt', type: 'string', length: 255, nullable: false)]
    private string $alt;

    //заголовок
    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: false)]
    private string $title;

    //описание
    #[ORM\Column(name: 'description', type: 'string', length: 1024, nullable: true)]
    private ?string $description = null;

    //показывать приложение или нет
    #[ORM\Column(name: 'is_shown', type: 'boolean', nullable: false, options: ['default' => false])]
    private bool $isShown = false;

    public function __construct(
        string $title,
        ?string $alt = null,
        ?string $description = null,
        bool $isShown = true
    ) {
        $this->title = $title;
        $this->alt = $alt;
        $this->description = $description;
        $this->isShown = $isShown;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isShown(): bool
    {
        return $this->isShown;
    }

    public function show(): void
    {
        $this->isShown = true;
    }

    public function hide(): void
    {
        $this->isShown = false;
    }

    // Wither-метод для изменения видимости (создает новый объект)
    public function withVisibility(bool $isShown): self
    {
        return new self($this->title, $this->alt, $this->description, $isShown);
    }
}
