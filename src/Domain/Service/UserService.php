<?php

namespace App\Domain\Service;

use App\Domain\Entity\User;
use InvalidArgumentException;
use App\Domain\Model\User\UserModel;
use App\Domain\ValueObject\Enum\UserRole;
use App\Domain\Model\User\CreateUserModel;
use App\Domain\Model\User\UpdateUserModel;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Controller\Web\Dashboard\User\EditUser\Input\EditUserDTO;
use App\Controller\Web\Dashboard\User\CreateUser\Input\CreateUserDTO;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly ModelFactory $modelFactory,
        private readonly GroupService $groupService,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly FileService $fileService,
    ) {
    }

    /**
     * @param int|null $groupId
     * @return UserModel[]
     */
    public function getChoicesForChoiceType(?int $groupId = null): array
    {
        // Получаем список выбора: [title => id]
        $choices = [];
        $plantModels = $this->userRepository->findAllByGroupId($groupId);
        foreach ($plantModels as $plant) {
            $choices[$plant->getTitle()] = $plant->getId();
        }

        return $choices;
    }

    /**
     * @param int $userId
     * @return ?User
     */
    public function find(int $userId): ?User
    {
        return $this->userRepository->find($userId);
    }

    /**
     * @return UserModel[]
     */
    public function findAll(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * @param string $email
     * @return UserModel[]
     */
    public function findUsersByEmail(string $email): array
    {
        return $this->userRepository->findUsersByEmail($email);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     * @throws InvalidArgumentException
 */
    public function getUsersPaginated(int $page, int $perPage): array
    {
        return $this->userRepository->getUsersPaginated($page, $perPage);
    }

    /**
     * @param CreateUserModel $createUserModel
     * @return UserModel
     * @throws InvalidArgumentException
     */
    public function create(CreateUserModel $createGroupModel): UserModel
    {
        $group = $this->groupService->find($createGroupModel->groupId);

        $user = new User(
            $group,
            $createGroupModel->email,
            $createGroupModel->password,
            $createGroupModel->lastName,
            $createGroupModel->firstName,
            $createGroupModel->middleName,
            $createGroupModel->roles,
            $createGroupModel->isActive,
            $createGroupModel->refreshToken,
            $createGroupModel->phone,
            $createGroupModel->avatarLink,
            $createGroupModel->emailCode,
            $createGroupModel->emailConfirmed,
            $createGroupModel->phoneCode,
            $createGroupModel->phoneConfirmed,
            $createGroupModel->timeZone
        );

        $user->changeFields(
            $group,
            $createGroupModel->email,
            $this->userPasswordHasher->hashPassword($user, $createGroupModel->password),
            $createGroupModel->lastName,
            $createGroupModel->firstName,
            $createGroupModel->middleName,
            $createGroupModel->roles,
            $createGroupModel->isActive,
            $createGroupModel->refreshToken,
            $createGroupModel->phone,
            $createGroupModel->avatarLink,
            $createGroupModel->emailCode,
            $createGroupModel->emailConfirmed,
            $createGroupModel->phoneCode,
            $createGroupModel->phoneConfirmed,
            $createGroupModel->timeZone
        );

        $this->userRepository->create($user);

        return $this->userRepository->toModel($user);
    }

    /**
     * @param CreateUserDTO $dto
     * @return UserModel
     */
    public function createFromCreateUserDTO(CreateUserDTO $dto): UserModel
    {
        // Проверяем уникальность email
        $existingUser = $this->userRepository->findUsersByEmail($dto->email);
        if ($existingUser) {
            throw new \InvalidArgumentException('Пользователь с таким email уже существует.');
        }

        $this->processFileForDTO($dto);

        $model = $this->modelFactory->makeModel(
            CreateUserModel::class,
                $dto->groupId,
                $dto->email,
                $dto->password,
                UserRole::toArray($dto->roles),
                $dto->isActive,
                $dto->emailConfirmed,
                $dto->phoneConfirmed,
                $dto->timeZone,
                $dto->lastName,
                $dto->firstName,
                $dto->middleName,
                $dto->refreshToken,
                $dto->phone,
                $dto->avatarLink,
                $dto->emailCode,
                $dto->phoneCode
            );

        return $this->create($model);
    }

    /**
     * @param User $user
     * @param UpdateUserModel $updateUserModel
     * @return UserModel
     * @throws InvalidArgumentException
     */
    public function update(User $user, UpdateUserModel $updateUserModel): UserModel
    {
        $group = $this->groupService->find($updateUserModel->groupId);

        $user->changeFields(
            $group,
            $updateUserModel->email,
            $this->userPasswordHasher->hashPassword($user, $updateUserModel->password),
            $updateUserModel->lastName,
            $updateUserModel->firstName,
            $updateUserModel->middleName,
            $updateUserModel->roles,
            $updateUserModel->isActive,
            $updateUserModel->refreshToken,
            $updateUserModel->phone,
            $updateUserModel->avatarLink,
            $updateUserModel->emailCode,
            $updateUserModel->emailConfirmed,
            $updateUserModel->phoneCode,
            $updateUserModel->phoneConfirmed,
            $updateUserModel->timeZone
        );

        $this->userRepository->update();

        return $this->userRepository->toModel($user);
    }

    /**
     * @param User $user
     * @param EditUserDTO $dto
     * @return void
     */
    public function updateFromEditUserDTO(User $user, EditUserDTO $dto): void
    {
        // Если загружен новый файл — обновляем путь и удаляем старый
        if ($dto->avatarFile instanceof UploadedFile) {
            $this->removeOldFile($user);
            $this->processFileForDTO($dto);
        }

        // Создаём модель обновления
        $model = $this->modelFactory->makeModel(
            UpdateUserModel::class,
                $dto->groupId,
                $dto->email,
                $dto->password,
                UserRole::toArray($dto->roles),
                $dto->isActive,
                $dto->emailConfirmed,
                $dto->phoneConfirmed,
                $dto->timeZone,
                $dto->lastName,
                $dto->firstName,
                $dto->middleName,
                $dto->refreshToken,
                $dto->phone,
                $dto->avatarLink,
                $dto->emailCode,
                $dto->phoneCode
            );

        // Выполняем обновление
        $this->update($user, $model);
    }

    /**
     * @param int $userId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $userId): void
    {
        $user = $this->userRepository->find($userId);
        $this->userRepository->remove($user);
    }

    /**
     * @param User $user
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeUser(User $user): void
    {
        $this->userRepository->remove($user);
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteWithFile(int $id): void
    {
        $user = $this->find($id);

        if (!$user) {
            throw new \InvalidArgumentException("Вложение с ID {$id} не найдено");
        }

        // Удаляем файл
        $this->removeOldFile($user);

        // Удаляем сущность
        $this->removeUser($user);
    }

    /**
     * Вспомогательные методы
     *
     * @param CreateUserDTO|EditUserDTO $dto
     * @return void
     */
    private function processFileForDTO(CreateUserDTO|EditUserDTO $dto): void
    {
        if ($dto->avatarFile instanceof UploadedFile) {
            $path = $this->fileService->getAttachmentsPath('user::class', $dto->groupId);
            $uploadedFile = $this->fileService->storeUploadedFile($dto->avatarFile, $path);

            $dto->avatarLink = $path . $uploadedFile->getFilename();
        }
    }

    /**
     * @param User $user
     * @return void
     */
    private function removeOldFile(User $user): void
    {
        if ($user->getAvatarLink()) {
            $this->fileService->removeUploadedFile($user->getAvatarLink());
        }
    }
}
