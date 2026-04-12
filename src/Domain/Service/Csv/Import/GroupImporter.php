<?php

namespace App\Domain\Service\Csv\Import;

use App\Domain\Service\GroupService;
use App\Domain\Service\ModelFactory;
use App\Domain\Model\Group\CreateGroupModel;

class GroupImporter extends AbstractCsvImporter implements EntityImporterInterface
{
    public function __construct(
        protected readonly ModelFactory $modelFactory,
        private readonly GroupService $groupService
    ) {
    }

    public function supports(string $fileName): bool
    {
        return $fileName === 'group';
    }

    public function import(array $data): void
    {
        $model = $this->modelFactory->makeModel(CreateGroupModel::class, ...$this->map($data));
        $this->groupService->create($model);
    }

    private function map(array $d): array
    {
        return [
            $this->str2bool($d['is_active']),
            $d['title'],
            $this->es($d['description'])
        ];
    }
}
