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

                    if ($rowNum > 1 && $key < count($row) - 1) {
                        $array[$keys[$key]] = $item;
                    }
                }

                if (!empty($array)) {
                    $plantModel = new CreatePlantModel(
                        $array['title'],
                        $array['room'],
                        $array['is_shown'],
                        $array['description'],
                        isset($array['purchase_date']) ? new DateTime($array['purchase_date']) : null,
                        isset($array['purchase_date']) ? new DateTime($array['vaccination_date']) : null,
                        isset($array['purchase_date']) ? new DateTime($array['planting_date']) : null,
                        $array['manufacturer'],
                        Price::fromString($array['price']),
                        $array['soil']
                    );
                    $this->plantService->create($plantModel);
                }

            }
        }

        return $rowNum;
    }
}
