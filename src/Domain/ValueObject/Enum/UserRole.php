<?php

namespace App\Domain\ValueObject\Enum;

enum UserRole: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_ADMIN = 'ROLE_ADMIN';
    case ROLE_MANAGER = 'ROLE_MANAGER';
    case ROLE_GUEST = 'ROLE_GUEST';

    public static function isValid(string $value): bool
    {
        return in_array($value, array_column(self::cases(), 'value'), true);
    }

    public static function getChoices(): array
    {
        return [
            'Пользователь' => self::ROLE_USER->value,
            'Администратор' => self::ROLE_ADMIN->value,
            'Менеджер' => self::ROLE_MANAGER->value,
            'Гость' => self::ROLE_GUEST->value,
        ];
    }

    public static function getLabel(string $value): string
    {
        return match ($value) {
            self::ROLE_USER->value => 'Пользователь',
            self::ROLE_ADMIN->value => 'Администратор',
            self::ROLE_MANAGER->value => 'Менеджер',
            self::ROLE_GUEST->value => 'Гость',
            default => 'Неизвестно',
        };
    }

    public static function toString(array $roles): ?string
    {
        $strings = [];

        foreach ($roles as $role) {
            if ($role instanceof self) {
                $strings[] = $role->value;
            } elseif (is_string($role)) {
                $strings[] = $role;
            } else {
                throw new \InvalidArgumentException('Role must be a string or instance of UserRole');
            }
        }

        return !empty($strings) ? implode(',', $strings) : null;
    }

    /**
     * Преобразует строку с ролями (например, "ROLE_USER,ROLE_ADMIN") в массив ролей.
     *
     * @param string|null $rolesString
     * @return array
     */
    public static function fromString(?string $rolesString): array
    {
        if (empty($rolesString)) {
            return [];
        }

        $roles = array_map('trim', explode(',', $rolesString));
        $result = [];

        foreach ($roles as $role) {
            if (self::isValid($role)) {
                $result[] = self::from($role);
            }
        }

        return $result;
    }
}
