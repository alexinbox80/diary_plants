<?php

namespace Unit\Domain\Service;

use App\Domain\Entity\Group;
use App\Domain\Entity\User;
use PHPUnit\Framework\TestCase;
use App\Domain\Service\FileService;
use App\Domain\Service\UserService;
use App\Domain\Model\User\UserModel;
use App\Domain\Service\GroupService;
use App\Domain\Service\ModelFactory;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\ValueObject\Enum\UserRole;
use App\Domain\Model\User\CreateUserModel;
use App\Domain\Model\User\UpdateUserModel;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[CoversClass(UserService::class)]
class UserServiceTest extends TestCase
{
    private UserRepositoryInterface|MockObject $repository;
    private UserPasswordHasherInterface|MockObject $passwordHasher;
    private GroupService|MockObject $groupService;
    private FileService|MockObject $fileService;
    private ModelFactory|MockObject $modelFactory;
    private UserService $service;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepositoryInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->groupService = $this->createMock(GroupService::class);
        $this->fileService = $this->createMock(FileService::class);
        $this->modelFactory = $this->createMock(ModelFactory::class);

        $this->service = new UserService(
            $this->repository,
            $this->modelFactory,
            $this->groupService,
            $this->passwordHasher,
            $this->fileService
        );
    }

    #[Test]
    public function testCreateSuccess(): void
    {
        $model = new CreateUserModel(
            groupId: 1,
            email: 'test@example.com',
            password: 'plainPassword',
            roles: [UserRole::ROLE_USER],
            isActive: true,
            emailConfirmed: true,
            phoneConfirmed: false,
            timeZone: 'Europe/Moscow',
            lastName: 'Иванов',
            firstName: 'Иван',
            middleName: 'Иванович',
            refreshToken: 'token123',
            phone: '+79991234567',
            avatarLink: null,
            emailCode: null,
            phoneCode: null
        );

        $group = $this->createMock(Group::class);
        $this->groupService->method('find')->with(1)->willReturn($group);

        // Настройка хэширования
        $this->passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($this->isInstanceOf(PasswordAuthenticatedUserInterface::class), 'plainPassword')
            ->willReturn('hashedPassword');

        $this->repository->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(User::class));

        $userModel = $this->createMock(UserModel::class);
        $this->repository->method('toModel')->willReturn($userModel);

        $result = $this->service->create($model);

        $this->assertSame($userModel, $result);
    }

    #[Test]
    public function testUpdateTogglesActivation(): void
    {
        $userEntity = $this->createMock(User::class);
        $model = new UpdateUserModel(
            groupId: 1,
            email: 'test@example.com',
            password: null, // Пароль не меняем
            roles: [UserRole::ROLE_ADMIN],
            isActive: false, // Тестируем деактивацию
            emailConfirmed: true,
            phoneConfirmed: true,
            timeZone: 'UTC',
            lastName: 'Ivanov',
            firstName: 'Ivan',
            middleName: null,
            refreshToken: 'new_token',
            phone: '1234567890',
            avatarLink: 'path/to/avatar.jpg'
        );

        $this->groupService->method('find')->willReturn($this->createMock(Group::class));

        // Проверка Fluent Interface и вызовов логики
        $userEntity->method('moveToGroup')->willReturn($userEntity);
        $userEntity->method('changeName')->willReturn($userEntity);
        $userEntity->method('changeRole')->willReturn($userEntity);
        $userEntity->method('updateRefreshToken')->willReturn($userEntity);
        $userEntity->method('setPhone')->willReturn($userEntity);
        $userEntity->method('setAvatarLink')->willReturn($userEntity);
        $userEntity->method('setTimeZone')->willReturn($userEntity);

        // Ожидаем вызов suspend(), так как isActive = false
        $userEntity->expects($this->once())->method('suspend');
        $userEntity->expects($this->never())->method('activate');

        $this->repository->expects($this->once())->method('update');

        $this->service->update($userEntity, $model);
    }

    #[Test]
    public function testDeleteWithFileRemovesAvatar(): void
    {
        $userId = 7;
        $user = $this->createMock(User::class);
        $user->method('getAvatarLink')->willReturn('uploads/avatar.png');

        $this->repository->method('find')->with($userId)->willReturn($user);

        // Ожидаем удаление файла физически
        $this->fileService->expects($this->once())
            ->method('removeUploadedFile')
            ->with('uploads/avatar.png');

        $this->repository->expects($this->once())->method('remove')->with($user);

        $this->service->deleteWithFile($userId);
    }
}
