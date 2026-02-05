<?php

namespace App\Domain\Service;

use App\Domain\Model\Attachment\CreateAttachmentModel;
use App\Domain\Model\Fertilizer\CreateFertilizerModel;
use App\Domain\Model\Group\CreateGroupModel;
use App\Domain\Model\Offspring\CreateOffspringModel;
use App\Domain\Model\Pest\CreatePestModel;
use App\Domain\Model\Plant\CreatePlantModel;
use App\Domain\Model\Status\CreateStatusModel;
use App\Domain\Model\Stimulant\CreateStimulantModel;
use App\Domain\Model\Task\CreateTaskModel;
use App\Domain\Model\Usage\CreateUsageModel;
use App\Domain\Model\User\CreateUserModel;
use App\Domain\ValueObject\Price;
use DateTimeImmutable;

class CsvService
{
    public function __construct(
        private readonly string $csvSeparator,
        private readonly ModelFactory $modelFactory,
        private readonly GroupService $groupService,
        private readonly UserService $userService,
        private readonly PlantService $plantService,
        private readonly StatusService $statusService,
        private readonly OffspringService $offspringService,
        private readonly TaskService $taskService,
        private readonly PestService $pestService,
        private readonly FertilizerService $fertilizerService,
        private readonly StimulantService $stimulantService,
        private readonly AttachmentService $attachmentService,
        private readonly UsageService $usageService
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

    private function createGroupModel(array $groupModel): CreateGroupModel
    {
        return $this->modelFactory
            ->makeModel(
                CreateGroupModel::class,
                $groupModel['title'],
                $this->str2bool($groupModel['is_active']),
                $groupModel['description'] !== '' ? $groupModel['description'] : null
            );
    }

    private function createUserModel(array $userModel): CreateUserModel
    {
        $rolesString = $userModel['roles'] ?? '';
        $roles = $rolesString !== '' ? explode(',', $rolesString) : [];

        return $this->modelFactory
            ->makeModel(
                CreateUserModel::class,
                (int) $userModel['group_id'],
                $userModel['email'],
                $userModel['password'],
                $roles,
                $this->str2bool($userModel['is_active']),
                $this->str2bool($userModel['email_confirmed']),
                $this->str2bool($userModel['phone_confirmed']),
                $userModel['time_zone'] ?? 'Europe/Moscow',
                $userModel['last_name'],
                $userModel['first_name'],
                $userModel['middle_name'] !== '' ? $userModel['middle_name'] : null,
                $userModel['refresh_token'] !== '' ? $userModel['refresh_token'] : null,
                $userModel['phone'] !== '' ? $userModel['phone'] : null,
                $userModel['avatar_link'] !== '' ? $userModel['avatar_link'] : null,
                $userModel['email_code'] !== '' ? $userModel['email_code'] : null,
                $userModel['phone_code'] !== '' ? $userModel['phone_code'] : null
            );
    }

    private function createPlantModel(array $plantModel): CreatePlantModel
    {
        return $this->modelFactory
            ->makeModel(
                CreatePlantModel::class,
                (int) $plantModel['group_id'],
                $plantModel['title'],
                $plantModel['room'],
                $plantModel['is_shown'],
                $plantModel['description'],
                $plantModel['purchase_date'] !== '' ? new DateTimeImmutable($plantModel['purchase_date']) : null,
                $plantModel['vaccination_date'] !== '' ? new DateTimeImmutable($plantModel['vaccination_date']) : null,
                $plantModel['planting_date'] !== '' ? new DateTimeImmutable($plantModel['planting_date']) : null,
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
                (int) $offspringModel['group_id'],
                (int) $offspringModel['plant_id'],
                $offspringModel['fruiting_date'] !== '' ? new DateTimeImmutable($offspringModel['fruiting_date']) : null,
                $offspringModel['flowering_date'] !== '' ? new DateTimeImmutable($offspringModel['flowering_date']) : null,
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
                new DateTimeImmutable($taskModel['date']),
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
                $pestModel['letter'],
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
                $fertilizerModel['letter'],
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
                (int) $stimulantModel['quantity'],
                $stimulantModel['letter'],
                $stimulantModel['manufacturer'] !== '' ? $stimulantModel['manufacturer'] : null,
                $stimulantModel['description'] !== '' ? $stimulantModel['description'] : null,
                $stimulantModel['comment'] !== '' ? $stimulantModel['comment'] : null
            );
    }

    private function createAttachmentModel(array $attachmentModel): CreateAttachmentModel
    {
        return $this->modelFactory
            ->makeModel(
                CreateAttachmentModel::class,
                (int) $attachmentModel['group_id'],
                (bool) $attachmentModel['is_shown'],
                empty($attachmentModel['filename']) ? null : $attachmentModel['filename'],
                empty($attachmentModel['file']) ? null : $attachmentModel['file'],
                empty($attachmentModel['mime_type']) ? null : $attachmentModel['mime_type'],
                $attachmentModel['alt'],
                $attachmentModel['title'],
                new DateTimeImmutable($attachmentModel['file_date']),
                $attachmentModel['attachable_id'],
                $attachmentModel['attachable_type'],
                $attachmentModel['description'] !== '' ? $attachmentModel['description'] : null,
            );
    }

    private function createUsageModel(array $usageModel): CreateUsageModel
    {
        return $this->modelFactory
            ->makeModel(
                CreateUsageModel::class,
                new DateTimeImmutable($usageModel['use_date']),
                $usageModel['plant_id'],
                $usageModel['comment'] !== '' ? $usageModel['comment'] : null,
                $usageModel['usable_id'] ,
                $usageModel['usable_type'] ,
            );
    }

    private function createEntity(array $array, string $fileName): void
    {
        switch ($fileName) {
            case 'group':
                $groupModel = $this->createGroupModel($array);
                $this->groupService->create($groupModel);
                break;
            case 'user':
                $userModel = $this->createUserModel($array);
                $this->userService->create($userModel);
                break;
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
            case 'attachment':
                $attachmentModel = $this->createAttachmentModel($array);
                $this->attachmentService->create($attachmentModel);
                break;
            case 'usage':
                $usageModel = $this->createUsageModel($array);
                $this->usageService->create($usageModel);
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

    private function str2bool(string $str): bool
    {
        return filter_var($str, FILTER_VALIDATE_BOOLEAN);
    }
}
