<?php

namespace App\Domain\Model\Plant;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Plant;
use App\Domain\ValueObject\OId;
use App\Domain\ValueObject\Price;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Usage\UsageModel;
use App\Domain\Model\Offspring\OffspringModel;
use App\Domain\Model\Repotting\RepottingModel;
use App\Domain\Model\Attachment\AttachmentModel;
use App\Domain\Model\Interfaces\AttachableModelInterface;

class PlantModel implements AttachableModelInterface
{
    public function __construct(
        private readonly int $id,
        private readonly int $groupId,
        private readonly OId $oid,
        private readonly string $title,
        private readonly string $room,
        private readonly bool $isShown = true,
        private readonly array $attachment = [],
        private readonly ?string $description = null,
        private readonly ?string $qrCodeLink = null,
        private readonly ?DateTimeImmutable $purchaseDate = null,
        private readonly ?DateTimeImmutable $vaccinationDate = null,
        private readonly ?DateTimeImmutable $plantingDate = null,
        private readonly ?string $seller = null,
        private readonly ?string $nursery = null,
        private readonly ?Price $price = null,
        private readonly ?Price $shippingCost = null,
        private readonly ?Price $packagingCost = null,
        private readonly ?string $soil = null,
        private readonly bool $isSold = false,
        private readonly ?DateTimeImmutable $sellingDate = null,
        private readonly ?Price $sellingPrice = null,
        private readonly ?string $comment = null,
        private readonly ?GroupModel $group = null,
        private readonly array $usage = [],
        private readonly array $offspring = [],
        private readonly array $repotting = [],
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getOid(): OId
    {
        return $this->oid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getRoom(): string
    {
        return $this->room;
    }

    public function isShown(): bool
    {
        return $this->isShown;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getQrCodeLink(): ?string
    {
        return $this->qrCodeLink;
    }

    public function getPurchaseDate(): ?DateTimeImmutable
    {
        return $this->purchaseDate;
    }

    public function getVaccinationDate(): ?DateTimeImmutable
    {
        return $this->vaccinationDate;
    }

    public function getPlantingDate(): ?DateTimeImmutable
    {
        return $this->plantingDate;
    }

    public function getSeller(): ?string
    {
        return $this->seller;
    }

    public function getNursery(): ?string
    {
        return $this->nursery;
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

    public function getSoil(): ?string
    {
        return $this->soil;
    }

    public function isSold(): bool
    {
        return $this->isSold;
    }

    public function getSellingDate(): ?DateTimeImmutable
    {
        return $this->sellingDate;
    }

    public function getSellingPrice(): ?Price
    {
        return $this->sellingPrice;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return AttachmentModel[]
     */
    public function getAttachment(): array
    {
        return $this->attachment;
    }

    public function getGroup(): ?GroupModel
    {
        return $this->group;
    }

    /**
     * @return UsageModel[]
     */
    public function getUsage(): array
    {
        return $this->usage;
    }

    /**
     * @return OffspringModel[]
     */
    public function getOffspring(): array
    {
        return $this->offspring;
    }

    /**
     * @return RepottingModel[]
     */
    public function getRepotting(): array
    {
        return $this->repotting;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param array $items Массив записей (например, все поливы)
     * @return array Массив с добавленным 6-м элементом-прогнозом
     */
    private function addForecastToWatering(array $items, DateTimeZone $timeZone): array
    {
        if (count($items) < 2) {
            return $items; // Недостаточно данных для расчета интервала
        }

        // 1. Извлекаем и парсим даты
        $dates = array_map(function ($item) {
            return \DateTimeImmutable::createFromFormat('d.m.Y', $item['use_date']);
        }, $items);

        // Сортируем даты по возрастанию (от старых к новым)
        usort($dates, fn($a, $b) => $a <=> $b);

        // 2. Вычисляем интервалы между последовательными датами
        $intervals = [];
        for ($i = 1; $i < count($dates); $i++) {
            $intervals[] = $dates[$i]->getTimestamp() - $dates[$i - 1]->getTimestamp();
        }

        // 3. Считаем средний интервал в секундах и переводим в дни
        $avgSeconds = array_sum($intervals) / count($intervals);
        $avgDays = (int) round($avgSeconds / 86400);

        // 4. Берем самую позднюю дату и прибавляем средний интервал
        $lastDate = max($dates);
        $forecastDate = $lastDate->modify("+$avgDays days");

        // 5. Клонируем структуру последнего элемента для сохранения метаданных
        $lastEntry = end($items);
        $forecastEntry = array_merge($lastEntry, [
            'id' => null,
            'icon_tag' => '',
            'use_date' => $forecastDate->format('d.m.Y'),
            'usable_name' => "Прогноз: " . $lastEntry['usable_name'],
            'comment' => "Средний интервал: $avgDays дн.",
            'marker_color' => '',
            'attachable' => null,
            'created_at' => (new DateTimeImmutable())->setTimezone($timeZone)->format('d.m.Y H:i:s'),
            'updated_at' => (new DateTimeImmutable())->setTimezone($timeZone)->format('d.m.Y H:i:s')
        ]);

        $items[] = $forecastEntry;

        return $items;
    }

    /**
     * Группирует массив по типам и оставляет только 5 последних события по дате
     */
    private function getLastFiveByType(array $data, DateTimeZone $timeZone): array
    {
        $grouped = [];

        // 1. Группируем элементы по полю usable_type
        foreach ($data as $item) {
            if ($item['attachable'] !== null) {
                $item['attachable'] = $item['attachable']->toArray();
            }
            $grouped[$item['usable_type']][] = $item;
        }

        // 2. Обрабатываем каждую группу отдельно
        return array_map(function(array $group) use ($timeZone) {
            // Сортируем внутри группы по дате (от старых к новым)
            usort($group, function($a, $b) {
                return strtotime($a['use_date']) <=> strtotime($b['use_date']);
            });

            // Оставляем только 5 последних
            $lastFive = array_slice($group, -5);

            // 3. Если это группа "watering" и в ней больше 1 записи — добавляем прогноз
            if (isset($lastFive[0]) && $lastFive[0]['usable_type'] === 'watering' && count($lastFive) >= 2) {
                $lastFive = $this->addForecastToWatering($lastFive, $timeZone);
            }

            return $lastFive;
        }, $grouped);
    }

    /**
     * @param Plant $plant
     * @param array $attachmentModels
     * @param GroupModel|null $groupModel
     * @param UsageModel[] $usageModels
     * @param OffspringModel[] $offspringModels
     * @param RepottingModel[] $repottingModels
     * @return PlantModel
     */
    public static function fromEntity(
        Plant $plant,
        array $attachmentModels = [],
        ?GroupModel $groupModel = null,
        array $usageModels = [],
        array $offspringModels = [],
        array $repottingModels = []
    ): self {
        return new self(
            $plant->getId(),
            $plant->getGroup()->getId(),
            $plant->getPlantIdentifier()->getOid(),
            $plant->getTitle(),
            $plant->getRoom(),
            $plant->isShown(),
            $attachmentModels,
            $plant->getDescription(),
            $plant->getPlantIdentifier()->getQrCodeLink(),
            $plant->getPurchaseInfo()->getPurchaseDate(),
            $plant->getLifeCycle()->getVaccinationDate(),
            $plant->getLifeCycle()->getPlantingDate(),
            $plant->getPurchaseInfo()->getSeller(),
            $plant->getPurchaseInfo()->getNursery(),
            $plant->getPurchaseInfo()->getPrice(),
            $plant->getPurchaseInfo()->getShippingCost(),
            $plant->getPurchaseInfo()->getPackagingCost(),
            $plant->getLifeCycle()->getSoil(),
            $plant->getSalesInfo()->isSold(),
            $plant->getSalesInfo()->getSellingDate(),
            $plant->getSalesInfo()->getSellingPrice(),
            $plant->getComment(),
            $groupModel,
            $usageModels,
            $offspringModels,
            $repottingModels,
            $plant->getCreatedAt(),
            $plant->getUpdatedAt()
        );
    }

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => '#',
            'group_id' => 'Идентификатор группы',
            'group_title' => 'Группа',
            'oid' => 'Универсальный идентификатор',
            'img_gallery' => 'Изображение',
            'title' => 'Название',
            'room' => 'Помещение',
            'is_shown' => 'Показать',
            'description' => 'Описание',
            'img_qr_code_link' => 'QR код',
            'purchase_date' => 'Дата покупки',
            'vaccination_date' => 'Дата прививки',
            'planting_date' => 'Дата посадки',
            'seller' => 'Продавец',
            'nursery' => 'Питомник',
            'price' => 'Стоимость',
            'shipping_cost' => 'Стоимость доставки',
            'packaging_cost' => 'Стоимость упаковки',
            'soil' => 'Грунт',
            'is_sold' => 'Продано',
            'selling_date' => 'Дата продажи',
            'selling_price' => 'Стоимость при продаже',
            'comment' => 'Комментарий',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления'
        ];
    }

    public static function getProfileHeaderRu(): array
    {
        return [
            //'id' => '#',
            'group_id' => 'Идентификатор группы',
            'group_title' => 'Группа',
            'oid' => 'Универсальный идентификатор',
            'img_gallery' => 'Изображение',
            'title' => 'Название',
            'room' => 'Помещение',
            'is_shown' => 'Показать',
            'description' => 'Описание',
            'img_qr_code_link' => 'QR код',
            'purchase_date' => 'Дата покупки',
            'vaccination_date' => 'Дата прививки',
            'planting_date' => 'Дата посадки',
            'seller' => 'Продавец',
            'nursery' => 'Питомник',
            'price' => 'Стоимость',
            'shipping_cost' => 'Стоимость доставки',
            'packaging_cost' => 'Стоимость упаковки',
            'soil' => 'Грунт',
            'is_sold' => 'Продано',
            'selling_date' => 'Дата продажи',
            'selling_price' => 'Стоимость при продаже',
            'comment' => 'Комментарий',
            'usage_watering' => 'Поливы',
            'usage_fertilizer' => 'Удобрения',
            'usage_pest' => 'Обработка от вредителей',
            'usage_stimulant' => 'Обработка стимуляторами',
            'repottings' => 'Пересадки',
            'offsprings' => 'Плоды',
            //'created_at' => 'Дата создания',
            //'updated_at' => 'Дата обновления'
        ];
    }

    public function toArray(): array
    {
        $timezone = new DateTimeZone('Europe/Moscow');

        $filtered = array_filter($this->getAttachment(), fn ($attachment) => $attachment->getMimeType() !== null);
        $imgGallery = array_map(fn ($attachment) => $attachment->toArray(), $filtered);

        return [
            'id' => $this->getId(),
            'group_id' => $this->getGroupId(),
            'group_title' => $this->getGroup()?->getTitle(),
            'oid' => $this->getOid()?->toString(),
            'img_gallery' => $imgGallery,
            'title' => $this->getTitle(),
            'room' => $this->getRoom(),
            'is_shown' => $this->isShown() ? 'Да' : 'Нет',
            'description' => $this->getDescription(),
            'img_qr_code_link' => (!empty($this->getQrCodeLink())) ? $this->getQrCodeLink() : null,
            'purchase_date' => $this->getPurchaseDate()?->setTimezone($timezone)->format('d.m.Y'),
            'vaccination_date' => $this->getVaccinationDate()?->setTimezone($timezone)->format('d.m.Y'),
            'planting_date' => $this->getPlantingDate()?->setTimezone($timezone)->format('d.m.Y'),
            'seller' => $this->getSeller(),
            'nursery' => $this->getNursery(),
            'price' => $this->getPrice()?->toString(),
            'shipping_cost' => $this->getShippingCost()?->toString(),
            'packaging_cost' => $this->getPackagingCost()?->toString(),
            'soil' => $this->getSoil(),
            'is_sold' => $this->isSold() ? 'Да' : 'Нет',
            'selling_date' => $this->getSellingDate()?->setTimezone($timezone)->format('d.m.Y'),
            'selling_price' => $this->getSellingPrice()?->toString(),
            'comment' => $this->getComment(),
            'attachment' => $this->getAttachment(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
        ];
    }

    public function profileToArray(): array
    {
        $timezone = new DateTimeZone('Europe/Moscow');

        $filtered = array_filter($this->getAttachment(), fn ($attachment) => $attachment->getMimeType() !== null);
        $imgGallery = array_map(fn ($attachment) => $attachment->toArray(), $filtered);

        $usages = array_map(fn(UsageModel $u) => $u->toArray(), $this->getUsage());
        if (!empty($usages)) {
            $usages = $this->getLastFiveByType($usages, $timezone);
        }

        $offsprings = array_map(fn(OffspringModel $o) => $o->toArray(), $this->getOffspring());
        $repottings = array_map(fn(RepottingModel $r) => $r->toArray(), $this->getRepotting());

        return [
            'id' => $this->getId(),
            'group_id' => $this->getGroupId(),
            'group_title' => $this->getGroup()?->getTitle(),
            'oid' => $this->getOid()?->toString(),
            'img_gallery' => $imgGallery,
            'title' => $this->getTitle(),
            'room' => $this->getRoom(),
            'is_shown' => $this->isShown() ? 'Да' : 'Нет',
            'description' => $this->getDescription(),
            'img_qr_code_link' => (!empty($this->getQrCodeLink())) ? $this->getQrCodeLink() : null,
            'purchase_date' => $this->getPurchaseDate()?->setTimezone($timezone)->format('d.m.Y'),
            'vaccination_date' => $this->getVaccinationDate()?->setTimezone($timezone)->format('d.m.Y'),
            'planting_date' => $this->getPlantingDate()?->setTimezone($timezone)->format('d.m.Y'),
            'seller' => $this->getSeller(),
            'nursery' => $this->getNursery(),
            'price' => $this->getPrice()?->toString(),
            'shipping_cost' => $this->getShippingCost()?->toString(),
            'packaging_cost' => $this->getPackagingCost()?->toString(),
            'soil' => $this->getSoil(),
            'is_sold' => $this->isSold() ? 'Да' : 'Нет',
            'selling_date' => $this->getSellingDate()?->setTimezone($timezone)->format('d.m.Y'),
            'selling_price' => $this->getSellingPrice()?->toString(),
            'comment' => $this->getComment(),
            'usage_watering' => $usages['watering'] ?? null,
            'usage_fertilizer' => $usages['fertilizer'] ?? null,
            'usage_pest' => $usages['pest'] ?? null,
            'usage_stimulant' => $usages['stimulant'] ?? null,
            'offsprings' => $offsprings,
            'repottings' => $repottings,
            //'attachment' => '',//$this->getAttachment(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
        ];
    }
}
