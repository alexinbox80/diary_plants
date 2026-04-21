<?php  //статистические показатели по поливам

namespace App\Domain\ValueObject\Analytic;

use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class IntervalMetrics
{
    //количество поливов
    #[ORM\Column(type: 'integer')]
    private int $count = 0;

    //средний интервал между поливами в днях
    #[ORM\Column(type: 'float')]
    private float $averageDays = 0.0;

    public function __construct(int $count = 0, float $averageDays = 0.0)
    {
        Assert::greaterThanEq($count, 0);
        $this->count = $count;

        Assert::greaterThanEq($averageDays, 0);
        $this->averageDays = $averageDays;
    }

    public function getAverageDays(): float
    {
        return $this->averageDays;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
