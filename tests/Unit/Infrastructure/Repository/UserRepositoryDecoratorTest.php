<?php

namespace Unit\Infrastructure\Repository;

use DateTimeImmutable;

use App\Domain\Entity\User;
use App\Domain\Entity\Group;
use PHPUnit\Framework\TestCase;
use App\Domain\Model\User\UserModel;
use App\Domain\ValueObject\User\Name;
use App\Domain\ValueObject\User\Phone;
use App\Domain\ValueObject\User\Email;
use App\Domain\Model\Group\GroupModel;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Infrastructure\Repository\UserRepository;
use App\Infrastructure\Repository\UserRepositoryDecorator;
use App\Infrastructure\Repository\GroupRepositoryDecorator;

#[CoversClass(UserRepositoryDecorator::class)]
class UserRepositoryDecoratorTest extends TestCase
{
    private UserRepository|MockObject $innerRepository;
    private GroupRepositoryDecorator|MockObject $groupRepo;
    private UserRepositoryDecorator $decorator;

    protected function setUp(): void
    {
        $this->innerRepository = $this->createMock(UserRepository::class);
        $this->groupRepo = $this->createMock(GroupRepositoryDecorator::class);

        $this->decorator = new UserRepositoryDecorator(
            $this->innerRepository,
            $this->groupRepo
        );
    }

    #[Test]
    public function testFindModelReturnsCorrectModel(): void
    {
        $userId = 1;
        $user = $this->createUserMock($userId);

        $this->innerRepository->method('find')->with($userId)->willReturn($user);

        $result = $this->decorator->findModel($userId);

        $this->assertInstanceOf(UserModel::class, $result);
        $this->assertSame($userId, $result->getId());
    }

    #[Test]
    public function testFindAllReturnsModelsWithRelations(): void
    {
        $user = $this->createUserMock(10);
        $this->innerRepository->method('findAll')->willReturn([$user]);

        $groupModel = $this->createMock(GroupModel::class);
        $this->groupRepo->method('toModel')->willReturn($groupModel);

        $results = $this->decorator->findAll();

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertInstanceOf(UserModel::class, $results[0]);
    }

    #[Test]
    public function testGetUsersPaginatedReturnsFormattedArray(): void
    {
        $user = $this->createUserMock(1);
        $paginationData = [
            'items' => [$user],
            'pagination' => ['total' => 1, 'page' => 1]
        ];

        $this->innerRepository->method('getUsersPaginated')->willReturn($paginationData);
        $this->groupRepo->method('toModel')->willReturn($this->createMock(GroupModel::class));

        $result = $this->decorator->getUsersPaginated(1, 10);

        $this->assertArrayHasKey('usersModel', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertInstanceOf(UserModel::class, $result['usersModel'][0]);
    }

    /**
     * Фабрика мока User для PHPUnit 12 через Stub
     */
    private function createUserMock(int $id): MockObject
    {
        $user = $this->getMockBuilder(UserStub::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getId', 'getGroup', 'getEmail', 'getName', 'getPhone',
                'getRoles', 'isActive', 'getAvatarLink', 'getTimeZone',
                'getCreatedAt', 'getUpdatedAt', 'getUserIdentifier',
                'getPassword', // ДОБАВЛЕНО
                'getRefreshToken' // ДОБАВЛЕНО на будущее
            ])
            ->getMock();

        $email = new Email('test@example.com');
        // Убедитесь, что порядок аргументов совпадает с вашим классом Name
        $name = new Name('Иванов', 'Иван', 'Иванович');
        $phone = new Phone('+79990001122');

        $user->method('getId')->willReturn($id);
        $user->method('getEmail')->willReturn($email);
        $user->method('getName')->willReturn($name);
        $user->method('getPhone')->willReturn($phone);
        $user->method('getRoles')->willReturn(['ROLE_USER']);
        $user->method('isActive')->willReturn(true);
        $user->method('getPassword')->willReturn('hashed_password'); // ИСПРАВЛЕНИЕ
        $user->method('getRefreshToken')->willReturn('token_abc');
        $user->method('getAvatarLink')->willReturn('/avatars/1.jpg');
        $user->method('getTimeZone')->willReturn('Europe/Moscow');
        $user->method('getGroup')->willReturn($this->createMock(Group::class));
        $user->method('getUserIdentifier')->willReturn((string)$email);

        $now = new DateTimeImmutable();
        $user->method('getCreatedAt')->willReturn($now);
        $user->method('getUpdatedAt')->willReturn($now);

        return $user;
    }
}

/**
 * Стаб для User.
 * Необходим для обхода ограничений PHPUnit 12 на типизированные свойства email и name.
 */
abstract class UserStub extends User
{
    public function getId(): int { return 0; }
    public function getEmail(): Email { return new Email('a@a.ru'); }
    public function getName(): Name { return new Name('a', 'b'); }
    public function getPassword(): string { return ''; }
    public function getRefreshToken(): ?string { return null; }
    public function getPhone(): ?Phone { return null; }
    public function getGroup(): Group { return Assert::createMock(Group::class); }
    public function getCreatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
    public function getUpdatedAt(): DateTimeImmutable { return new DateTimeImmutable(); }
}
