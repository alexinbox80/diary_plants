<?php

namespace App\Infrastructure\Repository;

use Random\RandomException;
use App\Domain\Entity\User;
use App\Domain\Model\User\UserModel;
use App\Domain\Repository\UserRepositoryInterface;


class UserRepositoryDecorator implements UserRepositoryInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly GroupRepositoryDecorator $groupRepository
    ) {
    }

    /**
     * @param User $user
     * @return string
     * @throws RandomException
     */
    public function updateUserRefreshToken(User $user): string
    {
        return $this->userRepository->updateUserRefreshToken($user);
    }

    /**
     * @param User $user
     * @return void
     */
    public function clearUserRefreshToken(User $user): void
    {
        $this->userRepository->clearUserRefreshToken($user);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array{usersModel: UserModel[], pagination: array}
     * @throws \Exception
     */
    public function getUsersPaginated(int $page, int $perPage): array
    {
        $usersPaginated = $this->userRepository->getUsersPaginated($page, $perPage);

        $usersModel = array_map(
            fn (User $user) => $this->toModel($user, true),
            $usersPaginated['items']
        );

        return [
            'usersModel' => $usersModel,
            'pagination' => $usersPaginated['pagination']
        ];
    }

    /**
     * @param int $userId
     * @return User|null
     */
    public function find(int $userId): ?User
    {
        return $this->userRepository->find($userId);
    }

    /**
     * @param int $userId
     * @return UserModel|null
     */
    public function findModel(int $userId): ?UserModel
    {
        $user = $this->userRepository->find($userId);

        return $this->toModel($user);
    }

    /**
     * @return userModel[]
     */
    public function findAll(): array
    {
        $users = $this->userRepository->findAll();

        return array_map(
            fn (User $user) => $this->toModel($user, true),
            $users
        );
    }

    /**
     * @param int|null $groupId
     * @return userModel[]
     */

    public function findAllByGroupId(?int $groupId = null): array
    {
        $users =  $this->userRepository->findAllByGroupId($groupId);

        return array_map(
            fn (User $user) => $this->toModel($user),
            $users
        );
    }

    /**
     * @param string $email
     * @return UserModel[]
     */
    public function findUsersByEmail(string $email): array
    {
        $users = $this->userRepository->findUsersByEmail($email);

        return array_map(
            fn (User $user) => $this->toModel($user),
            $users
        );
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findUserByEmail(string $email): ?User
    {
        return $this->userRepository->findUserByEmail($email);
    }

    /**
     * @param string $refreshToken
     * @return User|null
     */
    public function findUserByRefreshToken(string $refreshToken): ?User
    {
        return $this->userRepository->findUserByRefreshToken($refreshToken);
    }

    /**
     * @param User $user
     * @return int
     */
    public function create(User $user): int
    {
        return $this->userRepository->create($user);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->userRepository->update();
    }

    /**
     * @param User $user
     * @return void
     */
    public function remove(User $user): void
    {
        $this->userRepository->remove($user);
    }

    /**
     * @param User $user
     * @param bool $addRelations
     * @return UserModel
     */
    public function toModel(User $user, bool $addRelations = false): UserModel
    {
        $userModel = null;
        if ($addRelations) {
            $userModel = $this->groupRepository->toModel($user->getGroup());
        }

        return UserModel::fromEntity($user, $userModel);
    }
}
