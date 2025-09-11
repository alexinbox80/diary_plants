<?php

namespace App\Domain\Service;

use App\Domain\Model\Fertilizer\CreateFertilizerModel;
use App\Domain\Model\Offspring\CreateOffspringModel;
use App\Domain\Model\Pest\CreatePestModel;
use App\Domain\Model\Plant\CreatePlantModel;
use App\Domain\Model\Price;
use App\Domain\Model\Status\CreateStatusModel;
use App\Domain\Model\Stimulant\CreateStimulantModel;
use App\Domain\Model\Task\CreateTaskModel;
use DateTime;

class CsvService
{
    public function __construct(
        private readonly string $csvSeparator,
        private readonly ModelFactory $modelFactory,
        private readonly PlantService $plantService,
        private readonly StatusService $statusService,
        private readonly OffspringService $offspringService,
        private readonly TaskService $taskService,
        private readonly PestService $pestService,
        private readonly FertilizerService $fertilizerService,
        private readonly StimulantService $stimulantService
    ) {
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

    private function createStatusModel(array $statusModel): CreateStatusModel
    {
        return $this->modelFactory
            ->makeModel(
                CreateStatusModel::class,
                $statusModel['letter'],
                $statusModel['color'],
                $statusModel['description'],
                $statusModel['color_description']
            );
    }

    private function createOffspringModel(array $offspringModel): CreateOffspringModel
    {
        return $this->modelFactory
            ->makeModel(
                CreateOffspringModel::class,
                $offspringModel['plant_id'],
                $offspringModel['fruiting_date'] !== '' ? new DateTime($offspringModel['fruiting_date']) : null,
                $offspringModel['flowering_date'] !== '' ? new DateTime($offspringModel['flowering_date']) : null,
                (int) $offspringModel['mass'],
                $offspringModel['color'],
                $offspringModel['flavor'],
                (int) $offspringModel['quantity'],
                $offspringModel['comment'],
            );
    }

    private function createTaskModel(array $taskModel): CreateTaskModel
    {
        return $this->modelFactory
            ->makeModel(
                CreateTaskModel::class,
                (int) $taskModel['status_id'],
                (int) $taskModel['plant_id'],
                new DateTime($taskModel['date']),
                $taskModel['description']
            );
    }

    private function createPestModel(array $pestModel): CreatePestModel
    {
        return $this->modelFactory
            ->makeModel(
                CreatePestModel::class,
                (int) $pestModel['plant_id'],
                $pestModel['title'],
                (int) $pestModel['quantity'],
                new DateTime($pestModel['use_date']),
                $pestModel['manufacturer'] !== '' ? $pestModel['manufacturer'] : null,
                $pestModel['description'] !== '' ? $pestModel['description'] : null,
                $pestModel['comment'] !== '' ? $pestModel['comment'] : null
            );
    }

    private function createFertilizerModel(array $fertilizerModel): CreateFertilizerModel
    {
        return $this->modelFactory
            ->makeModel(
                CreateFertilizerModel::class,
                (int) $fertilizerModel['plant_id'],
                $fertilizerModel['title'],
                (int) $fertilizerModel['quantity'],
                new DateTime($fertilizerModel['use_date']),
                $fertilizerModel['manufacturer'] !== '' ? $fertilizerModel['manufacturer'] : null,
                $fertilizerModel['description'] !== '' ? $fertilizerModel['description'] : null,
                $fertilizerModel['comment'] !== '' ? $fertilizerModel['comment'] : null
            );
    }

    private function createStimulantModel(array $stimulantModel): CreateStimulantModel
    {
        return $this->modelFactory
            ->makeModel(
                CreateStimulantModel::class,
                (int) $stimulantModel['plant_id'],
                $stimulantModel['title'],
                $stimulantModel['manufacturer'],
                (int) $stimulantModel['quantity'],
                new DateTime($stimulantModel['use_date']),
                $stimulantModel['description'] === '' ?? null,
                $stimulantModel['comment'] === '' ?? null
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
                $statusModel = $this->createStatusModel($array);
                $this->statusService->create($statusModel);
                break;
            case 'offspring':
                $offspringModel = $this->createOffspringModel($array);
                $this->offspringService->create($offspringModel);
                break;
            case 'task':
                $taskModel = $this->createTaskModel($array);
                $this->taskService->create($taskModel);
                break;
            case 'pest':
                $pestModel = $this->createPestModel($array);
                $this->pestService->create($pestModel);
                break;
            case 'fertilizer':
                $fertilizerModel = $this->createFertilizerModel($array);
                $this->fertilizerService->create($fertilizerModel);
                break;
            case 'stimulant':
                $stimulantModel = $this->createStimulantModel($array);
                $this->stimulantService->create($stimulantModel);
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
