<?php

namespace App\Application\UI\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;
use App\Domain\ValueObject\Enum\UserRole;

class UserRoleExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('readable_role', [$this, 'formatRole']),
        ];
    }

    public function formatRole(array|string $roles): string
    {
        $role = is_array($roles) ? ($roles[0] ?? '') : $roles;
        return UserRole::getLabelKey($role);
    }
}
