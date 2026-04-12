<?php //отвечает за количество и норма расхода

namespace App\Domain\ValueObject\Preparation;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert as WebmozartAssert;

#[ORM\Embeddable]
class PreparationVolume
{
    //количество
    #[ORM\Column(name: 'amount', type: 'integer', nullable: false)]
    private int $amount;

    //норма расхода 5 мл на 10 л воды
    #[ORM\Column(name: 'application_rate', type: 'string', length: 50, nullable: true)]
    private ?string $applicationRate = null;

    public function __construct(
        int $amount,
        ?string $applicationRate = null
    ) {
        WebmozartAssert::greaterThan($amount, 0, 'Amount must be positive');
        $this->amount = $amount;

        if ($applicationRate !== null)
            WebmozartAssert::lengthBetween($applicationRate, 1, 50, 'Application rate must be between 1 and 50 characters long.');

        $this->applicationRate = $applicationRate;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getApplicationRate(): ?string
    {
        return $this->applicationRate;
    }
}
