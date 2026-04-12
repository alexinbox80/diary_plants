<?php

namespace App\Domain\Service\Csv\Import;

use App\Domain\Service\UserService;
use App\Domain\Service\ModelFactory;
use App\Domain\Model\User\CreateUserModel;

class UserImporter extends AbstractCsvImporter implements EntityImporterInterface
{
    public function __construct(
        protected readonly ModelFactory $modelFactory,
        private readonly UserService $userService
    ) {
    }

    public function supports(string $fileName): bool
    {
        return $fileName === 'user';
    }

    public function import(array $data): void
    {
        $model = $this->modelFactory->makeModel(CreateUserModel::class, ...$this->map($data));
        $this->userService->create($model);
    }

    private function map(array $d): array
    {
        $rolesString = $d['roles'] ?? '';
        $roles = $rolesString !== '' ? explode(',', $rolesString) : [];

        return [
            (int) $d['group_id'],
            $d['email'],
            $d['password'],
            $roles,
            $this->str2bool($d['is_active']),
            $this->str2bool($d['email_confirmed']),
            $this->str2bool($d['phone_confirmed']),
            $d['time_zone'] ?? 'Europe/Moscow',
            $d['last_name'],
            $d['first_name'],
            $this->es($d['middle_name']),
            $this->es($d['refresh_token']),
            $this->es($d['phone']),
            $this->es($d['avatar_link']),
            $this->es($d['email_code']),
            $this->es($d['phone_code'])
        ];
    }
}
