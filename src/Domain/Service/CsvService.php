<?php

namespace App\Domain\Service;

use App\Domain\Model\OId;
use App\Domain\Model\Plant\CreatePlantModel;
use stdClass;

class CsvService
{
//    public function __construct(
//        private readonly PlantService $plantService
//    )
//    {
//    }

    /**
     * @param string $filePath
     * @return \Generator
     * @throws \Exception
     */
    public static function convertCsv(string $filePath): \Generator
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

    public static function process(PlantService $plantService, string $filePath): int
    {
        $array = [];
        $keys = [];
        $rowNum = 0;
        foreach (self::convertCsv($filePath) as $rowNum => $row) {
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
                        $array['oid'],
                        $array['title'],
                        $array['room'],
                        $array['is_shown'],
                        $array['description'],
                        $array['purchase_date'],
                        $array['vaccination_date'],
                        $array['planting_date'],
                        $array['manufacturer'],
                        $array['price'],
                        $array['soil']
                    );
                    $plantService->create($plantModel);
                }

            }
        }
        return $rowNum;
    }
}
