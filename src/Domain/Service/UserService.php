<?php

namespace App\Domain\Service;

use App\Domain\Entity\User;
use InvalidArgumentException;
use App\Domain\ValueObject\Name;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\Phone;
use App\Domain\Model\User\UserModel;
use App\Domain\ValueObject\Enum\UserRole;
use App\Domain\Model\User\CreateUserModel;
use App\Domain\Model\User\UpdateUserModel;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Controller\Web\Dashboard\User\EditUser\Input\EditUserDTO;
use App\Controller\Web\Dashboard\User\CreateUser\Input\CreateUserDTO;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

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
    public function create(CreateUserModel $createUserModel): UserModel
    {
        $group = $this->groupService->find($createUserModel->groupId);

        // Создаем "пустышку" только для того, чтобы Hasher получил нужный тип данных
        $tempUser = new class implements PasswordAuthenticatedUserInterface {
            public function getPassword(): ?string { return null; }
        };

        $hashedPassword = $this->userPasswordHasher->hashPassword(
            $tempUser, // Symfony позволяет передать пустой объект или временный
            $createUserModel->password
        );

        $user = new User(
            $group,
            new Email($createUserModel->email),
            $hashedPassword,
            new Name(
                $createUserModel->lastName,
                $createUserModel->firstName,
                $createUserModel->middleName
            ),
            $createUserModel->roles,
        );

        $user
            ->setPhone(new Phone($createUserModel->phone))
            ->setTimeZone($createUserModel->timeZone)
            ->setAvatarLink($createUserModel->avatarLink)
            ->updateRefreshToken($createUserModel->refreshToken);

        if ($createUserModel->isActive) $user->activate();

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

        if ($updateUserModel->password) {
            $user->upgradePassword($this->userPasswordHasher->hashPassword($user, $updateUserModel->password));
        }

        $user
            ->moveToGroup($group)
            ->changeName(New Name(
                $updateUserModel->lastName,
                $updateUserModel->firstName,
                $updateUserModel->middleName))
            ->changeRole($updateUserModel->roles[0]->value)
            ->updateRefreshToken($updateUserModel->refreshToken)
            ->setPhone(new Phone($updateUserModel->phone))
            ->setAvatarLink($updateUserModel->avatarLink)
            ->setTimeZone($updateUserModel->timeZone);

        if ($updateUserModel->isActive)
            $user->activate();
        else
            $user->suspend();

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
