<?php

namespace App\Domain\ValueObject\Plant;


use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\ValueObject\Price;

#[ORM\Embeddable]
class SalesInfo
{
    //Дата продажи
    #[ORM\Column(name: 'selling_date', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $sellingDate = null;

    //Стоимость продажи
    #[ORM\Column(type: 'price', length:10, nullable: true)]
    private ?Price $sellingPrice = null;

    //Продано
    #[ORM\Column(name: 'is_sold', type: 'boolean', options: ['default' => false])]
    private bool $isSold = false;

    public function __construct(
        ?DateTimeImmutable $sellingDate = null,
        ?Price $sellingPrice = null,
        bool $isSold = false
    ) {
        $this->sellingDate = $sellingDate;
        $this->sellingPrice = $sellingPrice;
        $this->isSold = $isSold;
    }

    public function getSellingDate(): ?DateTimeImmutable
    {
        return $this->sellingDate;
    }

    public function getSellingPrice(): ?Price
    {
        return $this->sellingPrice;
    }

    public function isSold(): bool
    {
        return $this->isSold;
    }
}
