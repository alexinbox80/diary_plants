<?php // группирует поля ключ шаблона и параметры для подстановки в шаблон

namespace App\Domain\ValueObject\UserMessage;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final class MessageContent
{
    // ключ шаблона сообщения из message.<lang>.yaml
    #[ORM\Column(name: 'translation_key', type: 'string', length: 255)]
    private string $translationKey;

    // для хранения переменных данных, которые подставляются в шаблон при отправке
    #[ORM\Column(name: 'parameter', type: 'json')]
    private array $parameters = [];

    public function __construct(string $translationKey, array $parameters = [])
    {
        Assert::notEmpty($translationKey, 'Translation key cannot be empty.');

        $this->translationKey = $translationKey;
        $this->parameters = $parameters;
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
