<?php

namespace Unit\Domain\Service\Csv\Import;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\UserService;
use App\Domain\Model\User\CreateUserModel;
use App\Domain\Service\Csv\Import\UserImporter;

class UserImporterTest extends TestCase
{
    private ModelFactory $modelFactory;
    private UserService $userService;
    private UserImporter $importer;

    protected function setUp(): void
    {
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->userService = $this->createMock(UserService::class);

        $this->importer = new UserImporter(
            $this->modelFactory,
            $this->userService
        );
    }

    public function testSupportsReturnsTrueForUser(): void
    {
        $this->assertTrue($this->importer->supports('user'));
        $this->assertFalse($this->importer->supports('plant'));
    }

    public function testImportSuccessfullyMapsFullUserData(): void
    {
        // 1. Данные из CSV со всеми возможными полями
        $data = [
            'group_id' => '1',
            'email' => 'test@example.com',
            'password' => 'hashed_password',
            'roles' => 'ROLE_USER,ROLE_ADMIN', // Строка должна стать массивом
            'is_active' => '1',
            'email_confirmed' => 'true',
            'phone_confirmed' => '0',
            'time_zone' => 'Europe/Berlin',
            'last_name' => 'Иванов',
            'first_name' => 'Иван',
            'middle_name' => '', // es() -> null
            'refresh_token' => 'token123',
            'phone' => '+79990000000',
            'avatar_link' => '',
            'email_code' => '1234',
            'phone_code' => ''
        ];

        $expectedRoles = ['ROLE_USER', 'ROLE_ADMIN'];
        $mockModel = $this->createMock(CreateUserModel::class);

        // 2. Проверка: Factory получает данные в строгом соответствии с логикой map()
        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(
                CreateUserModel::class,
                1,                          // (int) group_id
                'test@example.com',         // email
                'hashed_password',          // password
                $expectedRoles,             // roles (array)
                true,                       // is_active
                true,                       // email_confirmed
                false,                      // phone_confirmed
                'Europe/Berlin',            // time_zone
                'Иванов',                   // last_name
                'Иван',                     // first_name
                null,                       // middle_name
                'token123',                 // refresh_token
                '+79990000000',             // phone
                null,                       // avatar_link
                '1234',                     // email_code
                null                        // phone_code
            )
            ->willReturn($mockModel);

        $this->userService->expects($this->once())
            ->method('create')
            ->with($mockModel);

        $this->importer->import($data);
    }

    public function testImportHandlesDefaultTimeZoneAndEmptyRoles(): void
    {
        $data = [
            'group_id' => '2',
            'email' => 'user@test.com',
            'password' => 'pass',
            'roles' => '',              // Пустые роли -> []
            'is_active' => '1',
            'email_confirmed' => '1',
            'phone_confirmed' => '1',
            // time_zone отсутствует
            'last_name' => 'Doe',
            'first_name' => 'John',
            'middle_name' => '', 'refresh_token' => '', 'phone' => '',
            'avatar_link' => '', 'email_code' => '', 'phone_code' => ''
        ];

        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(
                $this->anything(),
                2, 'user@test.com', 'pass',
                [],                         // Ожидаем пустой массив ролей
                true, true, true,
                'Europe/Moscow',            // Ожидаем дефолтный часовой пояс
                $this->anything(), $this->anything()
            )
            ->willReturn($this->createMock(CreateUserModel::class));

        $this->importer->import($data);
    }
}
