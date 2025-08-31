<?php

namespace App\Domain\Service;

use App\Domain\Model\Plant\CreatePlantModel;
use App\Domain\Model\Price;
use DateTime;

class CsvService
{
    public function __construct(
        private readonly PlantService $plantService
    )
    {
    }

    /**
     * @param string $filePath
     * @return \Generator
     * @throws \Exception
     */
    public function convertCsv(string $filePath): \Generator
    {
        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            throw new \Exception();
        }

        //fgetcsv($handle, separator: ';');
        // пока не достигнем конца файла
        while (!feof($handle)) {
            // читаем строку
            // и генерируем значение
            yield fgetcsv($handle, separator: ';');
        }

        // закрываем
        fclose($handle);
    }

    public function process(string $filePath): int
    {
        $array = [];
        $keys = [];
        $rowNum = 0;
        foreach ($this->convertCsv($filePath) as $rowNum => $row) {
            if (!empty($row)) {
                foreach ($row as $key => $item) {
                    if ($rowNum === 0 && $key < count($row) - 1) {
                        $keys[] = $item;
                    }

                    if ($rowNum >= 1 && $key < count($row) - 1) {
                        $array[$keys[$key]] = $item;
                    }
                }

                if (!empty($array)) {
                    $plantModel = new CreatePlantModel(
                        $array['title'],
                        $array['room'],
                        $array['is_shown'],
                        $array['description'],
                        $array['purchase_date'] !== '' ? new DateTime($array['purchase_date']) : null,
                        $array['purchase_date'] !== '' ? new DateTime($array['vaccination_date']) : null,
                        $array['purchase_date'] !== '' ? new DateTime($array['planting_date']) : null,
                        $array['seller'],
                        $array['nursery'],
                        $array['price'] !== '' ? Price::fromString($array['price']) : null,
                        $array['shipping_cost'] !== '' ? Price::fromString($array['shipping_cost']) : null,
                        $array['packaging_cost'] !== '' ? Price::fromString($array['packaging_cost']) : null,
                        $array['soil'],
                        $array['comment']
                    );
                    $this->plantService->create($plantModel);
                }
            }
        }

        return $rowNum;
    }
}
