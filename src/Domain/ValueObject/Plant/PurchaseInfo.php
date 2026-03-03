<?php //информация о приобретении растения

namespace App\Domain\ValueObject\Plant;

use App\Domain\ValueObject\Price;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class PurchaseInfo
{
    //стоимость
    #[ORM\Column(type: 'price', length:10, nullable: true)]
    private ?Price $price = null;

    //стоимость доставки
    #[ORM\Column(type: 'price', length:10, nullable: true)]
    private ?Price $shippingCost = null;

    //стоимость упаковки
    #[ORM\Column(type: 'price', length:10, nullable: true)]
    private ?Price $packagingCost = null;

    //продавец
    #[ORM\Column(name: 'seller', type: 'string', length: 255, nullable: true)]
    private ?string $seller = null;

    //питомник
    #[ORM\Column(name: 'nursery', type: 'string', length: 255, nullable: true)]
    private ?string $nursery = null;

    //дата покупки
    #[ORM\Column(name: 'purchase_date', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $purchaseDate = null;

    public function __construct(
        ?Price $price = null,
        ?Price $shippingCost = null,
        ?Price $packagingCost = null,
        ?string $seller = null,
        ?string $nursery = null,
        ?DateTimeImmutable $purchaseDate = null
    ) {
        $this->price = $price;
        $this->shippingCost = $shippingCost;
        $this->packagingCost = $packagingCost;
        $this->seller = $seller;
        $this->nursery = $nursery;
        $this->purchaseDate = $purchaseDate;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function getShippingCost(): ?Price
    {
        return $this->shippingCost;
    }

    public function getPackagingCost(): ?Price
    {
        return $this->packagingCost;
    }

    public function getSeller(): ?string
    {
        return $this->seller;
    }

    public function getNursery(): ?string
    {
        return $this->nursery;
    }

    public function getPurchaseDate(): ?DateTimeImmutable
    {
        return $this->purchaseDate;
    }
}
