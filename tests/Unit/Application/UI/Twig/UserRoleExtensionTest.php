<?php

namespace Unit\Application\UI\Twig;

use Twig\TwigFilter;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\ValueObject\Enum\UserRole;
use App\Application\UI\Twig\UserRoleExtension;

class UserRoleExtensionTest extends TestCase
{
    private UserRoleExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new UserRoleExtension();
    }

    #[Test]
    public function testGetFiltersContainsReadableRole(): void
    {
        $filters = $this->extension->getFilters();

        $this->assertCount(1, $filters);
        $this->assertInstanceOf(TwigFilter::class, $filters[0]);
        $this->assertEquals('readable_role', $filters[0]->getName());
    }

    /**
     * Тестируем передачу строки (одиночная роль)
     */
    #[Test]
    public function testFormatRoleWithSingleString(): void
    {
        // Предположим, для 'ROLE_ADMIN' метод getLabel вернет 'Администратор'
        // Если UserRole::getLabel статический, убедитесь, что он доступен в тесте
        $result = $this->extension->formatRole('ROLE_ADMIN');

        $this->assertEquals(UserRole::getLabel('ROLE_ADMIN'), $result);
    }

    /**
     * Тестируем передачу массива (стандартный формат Symfony Roles)
     */
    #[Test]
    public function testFormatRoleWithArray(): void
    {
        $roles = ['ROLE_USER', 'ROLE_ALLOWED_TO_SWITCH'];
        $result = $this->extension->formatRole($roles);

        // Должна взяться первая роль из массива
        $this->assertEquals(UserRole::getLabel('ROLE_USER'), $result);
    }

    /**
     * Тестируем пустой массив
     */
    #[Test]
    public function testFormatRoleWithEmptyArray(): void
    {
        $result = $this->extension->formatRole([]);

        // В коде ($roles[0] ?? '') вернет пустую строку.
        // Проверяем, что getLabel корректно это обработает.
        $this->assertEquals(UserRole::getLabel(''), $result);
    }
}

