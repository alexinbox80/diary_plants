<?php

namespace App\Domain\ValueObject\Enum;

use InvalidArgumentException;

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
        $choices = [];
        foreach (self::cases() as $case) {
            $choices[$case->labelKey()] = $case->value;
        }
        return $choices;
    }

    public function labelKey(): string
    {
        return match ($this) {
            self::ROLE_USER => 'user_role.user',
            self::ROLE_ADMIN => 'user_role.admin',
            self::ROLE_MANAGER => 'user_role.manager',
            self::ROLE_GUEST => 'user_role.guest',
        };
    }

    public static function getLabelKey(string $value): string
    {
        $case = self::tryFrom($value);

        return $case ? $case->labelKey() : 'user_role.unknown';
    }

    /**
     * Преобразует массив с ролями (например, "ROLE_USER, ROLE_ADMIN") в строку ролей.
     *
     * @return string
     */
    public static function AllRolesToString(): string
    {
        $roles = array_column(self::cases(), 'value');
        return implode(', ', $roles);
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
                throw new InvalidArgumentException('Role must be a string or instance of UserRole');
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
    public static function toArray(?string $rolesString): array
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

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this === self::ROLE_ADMIN;
    }

    /**
     * @return bool
     */
    public function isManager(): bool
    {
        return $this === self::ROLE_MANAGER;
    }

    /**
     * @return bool
     */
    public function isUser(): bool
    {
        return $this === self::ROLE_USER;
    }

    /**
     * @return bool
     */
    public function isGuest(): bool
    {
        return $this === self::ROLE_GUEST;
    }
}
