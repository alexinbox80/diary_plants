<?php

namespace App\Domain\Service;

use App\Domain\Model\Plant\CreatePlantModel;
use App\Domain\Model\Price;
use DateTime;

class CsvService
{
    public function __construct(
        private readonly string $csvSeparator,
        private readonly ModelFactory $modelFactory,
        private readonly PlantService $plantService,
    )
    {
    }

    /**
     * @param string $filePath
     * @return \Generator
     * @throws \Exception
     */
    private function convertCsv(string $filePath): \Generator
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
            yield fgetcsv($handle, separator: $this->csvSeparator);
        }

        // закрываем
        fclose($handle);
    }

    private function createPlantModel(array $plantModel): CreatePlantModel
    {
        return $this->modelFactory
            ->makeModel(
                CreatePlantModel::class,
                $plantModel['title'],
                $plantModel['room'],
                $plantModel['is_shown'],
                $plantModel['description'],
                $plantModel['purchase_date'] !== '' ? new DateTime($plantModel['purchase_date']) : null,
                $plantModel['purchase_date'] !== '' ? new DateTime($plantModel['vaccination_date']) : null,
                $plantModel['purchase_date'] !== '' ? new DateTime($plantModel['planting_date']) : null,
                $plantModel['seller'],
                $plantModel['nursery'],
                $plantModel['price'] !== '' ? Price::fromString($plantModel['price']) : null,
                $plantModel['shipping_cost'] !== '' ? Price::fromString($plantModel['shipping_cost']) : null,
                $plantModel['packaging_cost'] !== '' ? Price::fromString($plantModel['packaging_cost']) : null,
                $plantModel['soil'],
                $plantModel['comment']
            );
    }

    private function createEntity(array $array, string $fileName): void
    {
        switch ($fileName) {
            case 'plant':
                $plantModel = $this->createPlantModel($array);
                $this->plantService->create($plantModel);
                break;
            case 'status':

                break;
        }
    }

    public function process(string $filePath): int
    {
        $arr = explode('/', $filePath);
        [$fileNumber, $fileName, $fileExtension] = explode('.', end($arr));

        $keys = [];
        $rowNum = 0;
        $data = $this->convertCsv($filePath);
        foreach ($data as $rowNum => $row) {
            if (empty($row)) {
                continue;
            }

            if ($rowNum === 0) {
                // первая строка — заголовки
                $keys = $row;
            } else {
                // остальные строки — данные
                $array = array_combine($keys, $row);

                // пропускаем пустые массивы
                if (!empty($array)) {
                    $this->createEntity($array, $fileName);
                }
            }
        }

        return $rowNum - 1;
    }
}
